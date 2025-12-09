<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Changeetat;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Changeetat`.
* Hérite de `Changeetat`. Ajoutez ici vos propres méthodes.
*/
class ChangeetatEntity extends Changeetat
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=Changeetat::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Changeetat::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\ChangeetatEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getChangeetatBy($sqlF,$key,$value,$filter=null){
      $arr=Changeetat::getChangeetatBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\ChangeetatEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }