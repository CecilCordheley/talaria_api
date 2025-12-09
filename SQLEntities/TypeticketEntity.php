<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Typeticket;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Typeticket`.
* Hérite de `Typeticket`. Ajoutez ici vos propres méthodes.
*/
class TypeticketEntity extends Typeticket
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=Typeticket::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Typeticket::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\TypeticketEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getTypeticketBy($sqlF,$key,$value,$filter=null){
      $arr=Typeticket::getTypeticketBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\TypeticketEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }