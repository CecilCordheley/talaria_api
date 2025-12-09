<?php
     namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\ServiceEntity;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;

/**
 * Ensemble des fonctions asynchrone relative aux utilisateur
 */
abstract class AsyncUser{
    /**
     * retourne une Instance de SQL Factory
     */
    private static function getSQLFactory():SQLFactory{
        return new SQLFactory(null,"../include/config.ini");
    }
    private static function checkToken($roles){
        $userData = MiddleAgent::checkTokenAndRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
    }
    private static function selectUser(SQLFactory $sqlF,string $uuidUser):UserEntity{
        $user=UserEntity::getUserBy($sqlF,"uuidUser",$uuidUser);
            if($user===false){
                   echo json_encode(["result"=>"error","message"=>"user doesn't exist !"]);
            exit();
            }
            return $user;
    }
    public static function updateData($idUser,$key,$value){
        try{
            $sqlF=self::getSQLFactory();
            self::checkToken(["admin","manager"]);
            $user=self::selectUser($sqlF,$idUser);
            $currentData = json_decode($user->dataAgent, true);
    if (!is_array($currentData)) {
        $currentData = [];
    }
    if($value!="")
    $currentData[$key]=$value;
    else
        unset($currentData[$key]);
    $newData=json_encode($currentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $user->dataAgent= str_replace('"',"\\\"",$newData);
     $return=UserEntity::update($sqlF,$user);
     if($return){
        if($value!="")
            return "data $key with $value updated successful";
        else
            return "data $key has been removed";
     }else{
         echo json_encode(["result"=>"error","message"=>"data $key with $value updated failed"]);
            exit();
     }
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function updateUser($idUser){
        try{
            $sqlF=self::getSQLFactory();
            self::checkToken(["admin","manager"]);
            //Get User
            $user=UserEntity::getUserBy($sqlF,"uuidUser",$idUser);
            if($user===false){
                 echo json_encode(["result"=>"error","message"=>"user doesn't exist !"]);
            exit();
            }
            if($user->typeUser==1){
                 echo json_encode(["result"=>"error","message"=>"can't update this user !"]);
            exit();
            }
            $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
            
            $user->nomUser=isset($jsonData["nom"])?$jsonData["nom"]:$user->nomUser;
            $user->prenomUser=isset($jsonData["prenom"])?$jsonData["prenom"]:$user->prenomUser;
            $user->mailUser=isset($jsonData["mail"])?$jsonData["mail"]:$user->mailUser;
            $data=isset($jsonData["data"])?$jsonData["data"]:$user->dataAgent;
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
            $user->dataAgent= str_replace('"',"\\\"",$newData);
            if(UserEntity::update($sqlF,$user)){
                return "user updated !";
            }else{
                echo json_encode(["result"=>"error","message"=>"user update failed !!"]);
            exit();
            }
            
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    /**
     * associe un utilisateur à un service
     * @param string $user UUID de l'utilisateur
     * @param string $service numéro reférence du service
     * @return void
     */
    public static function associateService(string $user,string $service){
        try{
             $sqlF=self::getSQLFactory();
            self::checkToken(["admin","manager"]);
            //obtenir le service 
            $service=ServiceEntity::getServiceBy($sqlF,"uuidService",$service);
            if($service==false){
                 echo json_encode(["result"=>"error","message"=>"Service doesn't exist"]);
                 exit();
            }
            //obtenir l'utilisateur
            $user=UserEntity::getUserBy($sqlF,"uuidUser",$user);
           
            if($user->associateService($sqlF,$service)){
                return "user is associate to service";
            }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    /**
     * Créé un utilisateur
     * @param int $type
     */
    public static function createUser($type){
        try{
             $sqlF=self::getSQLFactory();
            self::checkToken(['admin','manager',"agent","dev"]);
        //Récupérer les données en POST
        $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
        $user=new UserEntity();
        $user->nomUser=$jsonData["nom"]??"nom_user";
        $user->prenomUser=$jsonData["prenom"]??"prenom_user";
        if(!isset($jsonData["mail"])){
             echo json_encode(["result"=>"error","message"=>"no mail param"]);
            exit();
        }
        $user->mailUser=$jsonData["mail"];
        if(!isset($jsonData["mdp"])){
             echo json_encode(["result"=>"error","message"=>"no mdp param"]);
            exit();
        }
        $crypto=new Cryptographer();
        $user->mdpUser=$crypto->hashString($jsonData["mdp"]);
        $user->typeUser=$type;
        $user->uuidUser=uniqid();
        $user->dataAgent="{}";
            $arrive = date("Y-m-d");
        $date = new DateTime($arrive);
        // Ajout de 1 mois
        $date->modify('+1 months');
        //durée de validité du mot de passe
        $valid = $date->format('Y-m-d');
        $user->validiteMdp=$valid;
        $return=UserEntity::add($sqlF,$user);
        if($return){
           return $user->uuidUser;
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la création"]);
            exit();
        }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function getUser($id=null){
        try{
              $sqlF=self::getSQLFactory();
             $userData = MiddleAgent::checkTokenAndRole(["admin","manager","agent","dev"]);
        $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
         if($user_required==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
         if($id==null){
            $return=UserEntity::getAll($sqlF);
            if(is_array($return))
             return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            unset($arr["idUser"]);
            $car[]=$arr;
            return $car;
        },[]);
            else{
                $arr=$return->getArray();
                unset($arr["idUser"]);
                return $arr;
            }
        }else{
            $return=UserEntity::getUserBy($sqlF,"uuidUser",$id);
             $arr=$return->getArray();
            unset($arr["idUser"]);
            return $arr;
        }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
       
    }
       public static function connexion($mail,$mdp){
        try{
         $session_manager=new SessionManager;
        $return=UserEntity::connexion(self::getSQLFactory(),
        $mail,$mdp,function($user) use($session_manager){
            $session_manager->set("user",$user);
        });
       
        if($return!=false){
            $log=new HistoryLog("../include/connexion.log");
            $log->addEntry($return["user_id"]."- connexion");
            $log->commit();
            $session_manager->set("isConnect","1");
            return $return;
        }
        return ["status"=>"error","message"=>"not a valid mail or pwd"];
    }catch(Exception $exception){
          echo json_encode(["status"=>"error","message"=>$exception->getMessage()]);
        exit();
    }
    }
}