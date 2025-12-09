<?php
 namespace apis\module\asyncModule;

use Exception;
use SQLEntities\EtatticketEntity;
use SQLEntities\TypeticketEntity;
use SQLEntities\TypeUserEntity;
use SQLEntities\UserEntity;

use Vendor\EasyFrameWork\Core\Master\MiddleAgent;
use vendor\easyFrameWork\Core\Master\SQLFactory;



abstract class AsyncData {
     private static function getSQLFactory(){
        return new SQLFactory(null,"../include/config.ini");
    } 
    public static function getStateTicket(){
        try{
            $sqlF=self::getSQLFactory();
             $userData = MiddleAgent::checkTokenAndRole(["admin","manager","agent","dev"]);
        $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
         if($user_required==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        $return=EtatticketEntity::getAll($sqlF);
         return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            unset($arr["idEtatTicket"]);
            $car[]=$arr;
            return $car;
        },[]);
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    /**
     * retourne les type de tickets
     */
    public static function getTypeTicket(){
        try{
        $sqlF=self::getSQLFactory();
        $userData = MiddleAgent::checkTokenAndRole(["admin","manager","agent","dev"]);
        $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
         if($user_required==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
         $return = TypeticketEntity::getAll($sqlF);
        return array_reduce($return,function($car,$el){
            $arr=$el->getArray();
            unset($arr["idTypeTicket"]);
            $car[]=$arr;
            return $car;
        },[]);
    }catch(Exception $e){
        echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
    }
    }
    /**
     * Retourne les types utilisateurs
     */
    public static function getTypeUser(){
        try{
        $sqlF=self::getSQLFactory();
        $userData = MiddleAgent::checkTokenAndRole(["admin","manager"]);
        $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user"]);
         if($user_required==false){
            echo json_encode(["result"=>"error","message"=>"no User Finded"]);
            exit();
        }
        $return = TypeUserEntity::getAll($sqlF);
        return array_reduce($return,function($car,$el){
             $arr=$el->getArray();
            unset($arr["idTypeUser"]);
            $car[]=$arr;
            return $car;
        },[]);
    }catch(Exception $e){
        echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
    }
    }
}