<?php
     namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\EntrepriseEntity;
use SQLEntities\Service;
use SQLEntities\ServiceEntity;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;

/**
 * Ensemble des fonctions asynchrone relative aux services
 */
abstract class asyncService{
        /**
     * retourne une Instance de SQL Factory
     */
    private static function getSQLFactory():SQLFactory{
        return new SQLFactory(null,"../include/config.ini");
    }
    public static function getTickets($idService){
         try{
            $sqlF=self::getSQLFactory();
            self::checkToken(['admin','manager','agent']);
            //Récupérer les données en POST
            $service=ServiceEntity::getServiceBy($sqlF,"idService",$idService);
            if($service===false){
                echo json_encode(["result"=>"error","message"=>"service $idService n'existe pas"]);
                exit();
            }
      //      var_dump($service);
           $return=$service->getTicketFrom($sqlF);
        
        if($return){
           return array_reduce($return,function($c,$e) use (&$sqlF){
            $t=$e->getArray();
            $auteur=UserEntity::getUserBy($sqlF,"idUser",$e->Auteur);
            $serv=ServiceEntity::getServiceBy($sqlF,"idService",$e->service);
            $t["Auteur"]=$auteur->uuidUser;
            $t["service"]=$serv->uuidService;
            $state=$e->lastState($sqlF);
            $c[]=["ticket"=>$t,"state"=>$state["etat_data"]->getArray()];
            return $c;
           },[]);
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite lors de l'exécution de la requête"]);
            exit();
        }
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    private static function checkToken($roles){
        $userData = MiddleAgent::checkTokenAndRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
    }
    public static function associateEntreprise($idService,$siretEntreprise){
        try{
            $sqlF=self::getSQLFactory();
            self::checkToken(['admin']);
            //Récupérer les données en POST
            $service=ServiceEntity::getServiceBy($sqlF,"uuidService",$idService);
            if($service===false){
                echo json_encode(["result"=>"error","message"=>"service $idService n'existe pas"]);
                exit();
            }
            $entreprise=EntrepriseEntity::getEntrepriseBy($sqlF,"siretEntreprise",$siretEntreprise);
            if($entreprise===false){
                echo json_encode(["result"=>"error","message"=>"Entreprise $entreprise n'existe pas"]);
                exit();
            }
            $service->Entreprise=$entreprise->idEntreprise;
            $return=ServiceEntity::update($sqlF,$service);
        if($return){
           return "service : $idService associé à Entrerprise : $siretEntreprise";
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la mise à jour"]);
            exit();
        }
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function toogleService($id){
              try{
           
             $sqlF=self::getSQLFactory();
            self::checkToken(['admin']);
        //Récupérer les données en POST
        $service=ServiceEntity::getServiceBy($sqlF,"uuidService",$id);
        if($service===false){
            echo json_encode(["result"=>"error","message"=>"Aucun service trouvé"]);
            exit();
        }
        $service->isActiv=$service->isActiv=="1"?0:1;
        $return=ServiceEntity::update($sqlF,$service);
        if($return){
           return "service state toogle";
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la mise à jour"]);
            exit();
        }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function updateService($id){
          try{
           
             $sqlF=self::getSQLFactory();
            self::checkToken(['admin',"dev"]);
        //Récupérer les données en POST
        $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
        $service=ServiceEntity::getServiceBy($sqlF,"uuidService",$id);
        $service->nomService=$jsonData["nom"]??$service->nomService;
        $service->descService=$jsonData["desc"]??$service->descService;
      /* $service->createAt=date("Y-m-d");
       $service->isActiv=1;
       $service->uuidService=uniqid();*/
        $service->Entreprise=$jsonData["entreprise"]??$service->Entreprise;
        $return=ServiceEntity::update($sqlF,$service);
        if($return){
           return "data updated !";
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la mise à jour"]);
            exit();
        }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function createService(){
          try{
             $sqlF=self::getSQLFactory();
            self::checkToken(['admin',"dev"]);
        //Récupérer les données en POST
        $jsonData = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
        $service=new ServiceEntity;
        $service->nomService=$jsonData["nom"]??"nom_service";
        $service->descService=$jsonData["desc"]??"desc_service";
       $service->createAt=date("Y-m-d");
       $service->isActiv=1;
       $service->uuidService=uniqid();
        $service->Entreprise=$jsonData["entreprise"]??NULL;
        $return=ServiceEntity::add($sqlF,$service);
        if($return){
           return $service->uuidService;
        }else{
            echo json_encode(["result"=>"error","message"=>"Une erreur s'est produite dans la création"]);
            exit();
        }
        }catch(Exception $e){
              echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function getService($id=null){
       try{
        $sqlF=self::getSQLFactory();
        self::checkToken(["admin","manager","agent","dev"]);
        if($id==null){
                $return=ServiceEntity::getAll($sqlF);
            if(is_array($return))
             return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            unset($arr["idService"]);
            $car[]=$arr;
            return $car;
        },[]);
            else{
                $arr=$return->getArray();
                unset($arr["idService"]);
                return $arr;
            }
        }else{
             $return=ServiceEntity::getServiceBy($sqlF,"uuidService",$id);
             $arr=$return->getArray();
            unset($arr["idService"]);
            return $arr;
        }
       }catch(Exception $e){
         echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
       }
    }
}