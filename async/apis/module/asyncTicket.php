<?php
   namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\ChangeetatEntity;
use SQLEntities\EntrepriseEntity;
use SQLEntities\EtatticketEntity;
use SQLEntities\Service;
use SQLEntities\ServiceEntity;
use SQLEntities\TicketEntity;
use SQLEntities\TypeticketEntity;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;

abstract class AsyncTicket{
         /**
     * retourne une Instance de SQL Factory
     */
    private static function getSQLFactory():SQLFactory{
        return new SQLFactory(null,"../include/config.ini");
    }
    private static function checkMissingData($data,$keys){
        foreach($keys as $k){
        if(!array_key_exists($k, $data)){
         echo json_encode(["result"=>"error","message"=>"data '$k' is missing"]);
        exit;
        }
    }
    }
    private static function checkToken($roles):UserEntity{
        $userData = MiddleAgent::checkTokenAndRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
            return $user_required;
    }
    public static function getTickets($filter = null) {
    try {
        $sqlF = self::getSQLFactory();
        $user = self::checkToken(["manager", "agent"]);

        // --- Récupération des tickets ---
        $arr = TicketEntity::getAll($sqlF);
        $tickets = is_array($arr) ? $arr : [$arr];

        // --- Transformation des tickets ---
        $i = 0;
        $return2_arr = array_reduce($tickets, function ($carry, $ticket) use (&$i, $sqlF) {

            $t = $ticket->getArray();

            // Convert Auteur, Service, TypeTicket
            $t["Auteur"] = UserEntity::getUserBy($sqlF, "idUser", $t["Auteur"])->uuidUser;
            $t["Service"] = ServiceEntity::getServiceBy($sqlF, "idService", $t["service"])->uuidService;
            $t["TypeTicket"] = TypeticketEntity::getTypeticketBy($sqlF, "idTypeTicket", $t["typeTicket"])->refTypeTicket;

            unset($t["service"], $t["typeTicket"], $t["idTicket"]);

            // États
            $states = $ticket->getEtats($sqlF);
            $t["states"] = array_map(fn($s) => $s->getArray(), $states);

            $carry[$i] = $t;
            $i++;
            return $carry;

        }, []);

        // --- Application des filtres ---
        if ($filter != null && is_array($filter)) {

            $return2_arr = array_filter($return2_arr, function ($ticket) use ($filter) {

                foreach ($filter as $field => $expectedValue) {

                    // Si le ticket n’a pas ce champ, on ignore
                    if (!isset($ticket[$field])) {
                        return false;
                    }

                    // Supporte les filtres simples : "Auteur" => "uuid"
                    // Et les filtres multiples : "Auteur" => ["uuid1", "uuid2"]
                    if (is_array($expectedValue)) {
                        if (!in_array($ticket[$field], $expectedValue)) {
                            return false;
                        }
                    } else {
                        if ($ticket[$field] != $expectedValue) {
                            return false;
                        }
                    }
                }

                return true;
            });
        }

        return array_values($return2_arr);

    } catch (Exception $e) {
        echo json_encode(["result" => "error", "message" => $e->getMessage()]);
        exit();
    }
}

    public static function updateTicket($idTiket){
        try{
            $sqlF=self::getSQLFactory();
            $user= self::checkToken(["manager","agent"]);
           //Need good ticket and good state
            $ticket=TicketEntity::getTicketBy($sqlF,"uuidTicket",$idTiket);
            if($ticket===false){
              echo json_encode(["result"=>"error","message"=>"Not a valid ticket"]);
              exit();
            }
            //Check the last State ===Have to be Created===
            $last=$ticket->lastState($sqlF);
            var_dump($last["etat_data"]->idEtatTicket);
            if($last["etat_data"]->idEtatTicket>1){
                echo json_encode(["result"=>"error","message"=>"Cannot update ticket state". $last["etat_data"]->libEtatTicket]);
              exit();
            }
            $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
            $ticket->contentTicket=$jsonData["content"]??$ticket->contentTicket;
            $ticket->objetTicket=$jsonData["objet"]??$ticket->objetTicket;
            $ticket->prioriteTicket=$jsonData["priorité"]??$ticket->prioriteTicket;
            $data=$jsonData["data"];
            if($data!=null){
                if (!is_array($data)) {
                    // Nettoyage basique du JSON
                    $data = preg_replace("/'/", '"', $data); // remplace les simples quotes par doubles
                    $data = preg_replace('/([{,])\s*([a-zA-Z0-9_]+)\s*:/', '$1"$2":', $data); // ajoute des quotes autour des clés si manquantes

                    $decoded = json_decode($data, true);
                }
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("$decoded Le JSON passé dans dataAgent est invalide : " . json_last_error_msg());
                }
                $newData=json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $ticket->dataticket= str_replace('"',"\\\"",$newData);
            }
            if(TicketEntity::update($sqlF,$ticket)){
                return "Ticket Updated !";
            }else{
                echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite"]);
              exit();
            }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    /**
     * Change l'état d'un ticket
     * @param string $idTiket reférence du TICKET (UUID)
     * @param string $newState référence d'état (ref etat ticket)
     * @param string $comment commentaire associé
     * @return void
     */
    public static function changeState(string $idTiket,string $newState,string $comment){
        try{
            $sqlF=self::getSQLFactory();
           $user= self::checkToken(["manager","agent"]);
           //Need good ticket and good state
           $ticket=TicketEntity::getTicketBy($sqlF,"uuidTicket",$idTiket);
           if($ticket===false){
            echo json_encode(["result"=>"error","message"=>"Not a valid ticket"]);
            exit();
           }
           $state=EtatticketEntity::getEtatticketBy($sqlF,"refEtatTicket",$newState);
           if($state===false){
            echo json_encode(["result"=>"error","message"=>"Not a valid state"]);
            exit();
           }
           if($ticket->changeEtat($sqlF,$state,$comment)){
                return "$idTiket change state to $newState";
           }else{
                echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite !"]);
            exit();
           }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function createTicket(){
        try{
           $sqlF=self::getSQLFactory();
           $user= self::checkToken(["agent"]);
        //Récupérer les données en POST
        $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
        self::checkMissingData($jsonData,["content","object","priority","data","service","type"]);
       $ticket=new TicketEntity();
       $ticket->uuidTicket=uniqid("TICKT-");
       $ticket->dateTicket=date("Y-m-d ");
       $ticket->Auteur=$user->idUser;
       $ticket->contentTicket=$jsonData["content"];
       $ticket->objetTicket=$jsonData["object"];
       $ticket->prioriteTicket=$jsonData["priority"];
    //   $ticket->typeTicket=$jsonData["type"];
       $data=$jsonData["data"];
            if (!is_array($data)) {
    // Nettoyage basique du JSON
    $data = preg_replace("/'/", '"', $data); // remplace les simples quotes par doubles
    $data = preg_replace('/([{,])\s*([a-zA-Z0-9_]+)\s*:/', '$1"$2":', $data); // ajoute des quotes autour des clés si manquantes

    $decoded = json_decode($data, true);
            }
             if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("$decoded Le JSON passé dans dataAgent est invalide : " . json_last_error_msg());
            }
            $newData=json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
       $ticket->dataticket= str_replace('"',"\\\"",$newData);
       $service=ServiceEntity::getServiceBy($sqlF,"uuidService",$jsonData["service"]);
       if($service===false){
        echo json_encode(["result"=>"error","message"=>"No Service find"]);
        exit;
       }
       $type=TypeticketEntity::getTypeticketBy($sqlF,"refTypeTicket",$jsonData["type"]);
       if($type===false){
        echo json_encode(["result"=>"error","message"=>"No type find"]);
        exit;
       }
       $ticket->typeTicket=$type->idTypeTicket;
       $ticket->service=$service->idService;
       if(TicketEntity::add($sqlF,$ticket,function(TicketEntity $ticket) use ($sqlF){
        $stateTiket=new ChangeetatEntity();
        $stateTiket->Ticket=$ticket->idTicket;
        $stateTiket->EtatTicket=1;
        $stateTiket->dateEtatTicket=date("Y-m-d H:i:s");
        $stateTiket->comment="Nouveau Ticket";
        if(!ChangeetatEntity::add($sqlF,$stateTiket)){
            echo json_encode(["result"=>"error","message"=>"default state ticket can be setup !"]);
            exit();
        }
       })){
        return $ticket->uuidTicket;
       }else{
         echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la création"]);
            exit();
       }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
}