<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Service;
use vendor\easyFrameWork\Core\Main;
use SQLEntities\UserEntity;
use SQLEntities\TicketEntity;
use Exception;
/**
* Class personnalisée pour la table `Service`.
* Hérite de `Service`. Ajoutez ici vos propres méthodes.
*/
class ServiceEntity extends Service
{
   /**
    * Retourne tout les utilistateurs du service
    * @param \vendor\easyFrameWork\Core\Master\SQLFactory $sqlF
    */
   public function getUsers(SQLFactory $sqlF){
    return UserEntity::getUserBy($sqlF,"service",$this->idService);
   }
   /**
    * retourne tout les ticket destiné au service
    * @param \vendor\easyFrameWork\Core\Master\SQLFactory $sqlF
    */
   public function getTickets(SQLFactory $sqlF){
    return TicketEntity::getTicketBy($sqlF,"service",$this->idService);
   }
   public static function getAll($sqlF){
    $arr=Service::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Service::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\ServiceEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\ServiceEntity");
    }else
    return false;
  }
    public static function getServiceBy($sqlF,$key,$value,$filter=null){
      $arr=Service::getServiceBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\ServiceEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\ServiceEntity");
    }else{
      return false;
    }
      }
 }