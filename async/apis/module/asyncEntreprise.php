<?php 
 namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\EntrepriseEntity;
use SQLEntities\ServiceEntity;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
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
        $userData = MiddleAgent::checkTokenAndRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
    }
    public static function setData($id,$key,$value){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin"]);
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
            self::checkToken(["manager","admin"]);
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
            $entreprise->update_At=date("Y-m-d H:i:s");
            return EntrepriseEntity::update($sqlf,$entreprise);
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function addEntreprise(){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin"]);
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
            $entreprise->created_At=date("Y-m-d");
            $entreprise->typeEntreprise=$jsonData["type"]??"autre";
            $entreprise->dataEntreprise="{}";
            return EntrepriseEntity::add($sqlf,$entreprise);
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function getEntreprise($id=null){
        try{
            $sqlf=self::getSQLFactory();
            self::checkToken(["manager","admin","agent"]);
            if($id===null){
                 $return=EntrepriseEntity::getAll($sqlf);
            }else{
                $return=EntrepriseEntity::getEntrepriseBy($sqlf,"siretEntreprise",$id);
            }
            if(is_array($return))
             return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            unset($arr["idEntreprise"]);
            $car[]=$arr;
            return $car;
        },[]);
            else{
                $arr=$return->getArray();
                unset($arr["idEntreprise"]);
                return $arr;
            }
        }catch(Exception $e){
            echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
                 exit();
        }
    }
}