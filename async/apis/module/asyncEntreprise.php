<?php 
 namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\EntrepriseEntity;
use SQLEntities\ServiceEntity;
use SQLEntities\UserEntity;
use SQLEntities\UserEntrepriseEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\DbTokenManager;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgentV2;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;
/**
 * Ensemble des fonction de gestion des entreprises Asynchrone
 */
class AsyncEntreprise{
       private static function getSQLFactory():SQLFactory{
        return new SQLFactory(null,"../include/config.ini");
    }
    private static function checkToken($roles){
        $userData = MiddleAgentV2::checkRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user_id"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
    
    }
    /**
     * Récupère les agents d'une entreprise
     * @param string $id Id de l'entreprise (SQL:idEntreprise)
     * @return mixed
     */
    public static function getUsers(string $id){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","dev"]);
            $entreprise=EntrepriseEntity::getEntrepriseBy($sqlf,"idEntreprise",$id);
             if($entreprise===false){
                echo json_encode(["result"=>"error","message"=>"entreprise introuvable"]);
                exit();
            }
            $users=$entreprise->getUsers($sqlf);
            if($users===false){
                echo json_encode(["result"=>"error","message"=>"no users"]);
                exit();
            }
            if($users){
                if(is_array($users)){
                    $a=[];
            $a=array_reduce($users,function($c,$e){
                $u=$e->getArray();
                unset($u["idUser"]);
                unset($u["mdpUser"]);
                unset($u["validateMdp"]);
                $c[]=$u;
        return $c;
            
        
            },[]);
            }else{
                $a[0]=$users->getArray();
                unset($a[0]["idUser"]);
                unset($a[0]["mdpUser"]);
                unset($a[0]["validateMdp"]);
            }
            return["Entrperise"=>$entreprise->siretEntreprise,"users"=>$a];
        }
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function delEntreprise(string $siret){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","dev"]);
            $entreprise=EntrepriseEntity::getEntrepriseBy($sqlf,"siretEntreprise",$siret);
            if($entreprise===false){
                echo json_encode(["result"=>"error","message"=>"entreprise introuvable"]);
                exit();
            }
            //Vérifie s'il y a au moins un service associé
            if(ServiceEntity::getServiceBy($sqlf,"Entreprise",$entreprise->idEntreprise)!=false){
                echo json_encode(["result"=>"error","message"=>"Services associés avec cette entreprise"]);
                exit();
            }
            //Vérifie s'il y a au moins un agent associé
            if(UserEntrepriseEntity::getUserEntrepriseBy($sqlf,"entreprise_idEntreprise",$entreprise->idEntreprise)!=false){
                echo json_encode(["result"=>"error","message"=>"Agents associés avec cette entreprise"]);
                exit();
            }
            $token=MiddleAgentV2::checkToken(true);
            DbTokenManager::refresh($token);
            return EntrepriseEntity::del($sqlf,$entreprise);
            
            
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function setData(string $id,string $key,mixed $value){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","dev"]);
            $entreprise=EntrepriseEntity::getEntrepriseBy($sqlf,"siretEntreprise",$id);
            if($entreprise===false){
                echo json_encode(["result"=>"error","message"=>"entreprise introuvable"]);
                exit();
            }
            $data=json_decode($entreprise->dataEntreprise,true);
            if(isset($data[$key])){
                if($value!=" ")
                    $data[$key]=$value;
                else
                    unset($data[$key]);
            }else{
                $data[$key]=$value;
            }
            $newData=json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $entreprise->dataEntreprise=str_replace('"',"\\\"",$newData);
            $return=EntrepriseEntity::update($sqlf,$entreprise);
            if($return){
                return "$key updata with $value";
            }else{
                return false;
            }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function update($id){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","dev"]);
            $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
            $entreprise=EntrepriseEntity::getEntrepriseBy($sqlf,"siretEntreprise",$id);
            if($entreprise===false){
                echo json_encode(["result"=>"error","message"=>"entreprise introuvable"]);
                exit();
            }
            $entreprise->nomEntreprise=$jsonData["nom"]??$entreprise->nomEntreprise;
            $entreprise->adresseEntrerprise=$jsonData["adresse"]??$entreprise->adresseEntrerprise;
            $entreprise->cpEntreprise=$jsonData["cp"]??$entreprise->cpEntreprise;
            $entreprise->villeEntreprise=$jsonData["ville"]??$entreprise->villeEntreprise;
            $entreprise->typeEntreprise=$jsonData["type"]??$entreprise->typeEntreprise;
            $entreprise->telEntreprise=$jsonData["tel"]??$entreprise->telEntreprise;
            $entreprise->mailEntreprise=$jsonData["mail"]??$entreprise->mailEntreprise;
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
            $entreprise->dataEntreprise=str_replace('"',"\\\"",$newData);
            $entreprise->update_At=date("Y-m-d H:i:s");
            $token=MiddleAgentV2::checkToken(true);
            DbTokenManager::refresh($token);
            return EntrepriseEntity::update($sqlf,$entreprise);
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function addEntreprise(){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","dev"]);
            $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
            $entreprise=new EntrepriseEntity();
            $entreprise->nomEntreprise=$jsonData["nom"]??"nom_entreprise";
            if(!isset($jsonData["siret"])){
                 echo json_encode(["result"=>"error","message"=>"siret obligatoire"]);
            exit();
            }
            $entreprise->siretEntreprise=$jsonData["siret"]??"0000000000";
            $entreprise->adresseEntrerprise=$jsonData["adresse"]??"adresse entreprise";
            $entreprise->cpEntreprise=$jsonData["cp"]??"00000";
            $entreprise->villeEntreprise=$jsonData["ville"]??"ville entreprise";
            $entreprise->created_At=date("Y-m-d H:i:s");
            $entreprise->mailEntreprise=$jsonData["mail"]??"NULL";
            $entreprise->telEntreprise=$jsonData["tel"]??"NULL";
            $entreprise->typeEntreprise=$jsonData["type"]??"autre";
            $entreprise->dataEntreprise="{}";
            $token=MiddleAgentV2::checkToken(true);
            DbTokenManager::refresh($token);
            return EntrepriseEntity::add($sqlf,$entreprise);
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    
    public static function getEntreprise(mixed $id=null){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","agent","dev"]);
            if($id===null){
                 $return=EntrepriseEntity::getAll($sqlf);
            }else{
                if(strlen($id)!=14){
                    $return=EntrepriseEntity::getEntrepriseBy($sqlf,"idEntreprise",$id);
                }else
                    $return=EntrepriseEntity::getEntrepriseBy($sqlf,"siretEntreprise",$id);
            }
          // var_dump($return);
            if(is_array($return))
             return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            $car[]=$arr;
            return $car;
        },[]);
            else{
                $arr=$return->getArray();
                return $arr;
            }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
                 exit();
        }
    }
}