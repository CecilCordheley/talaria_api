<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\TypeUser;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `TypeUser`.
* Hérite de `TypeUser`. Ajoutez ici vos propres méthodes.
*/
class TypeUserEntity extends TypeUser
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=TypeUser::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(TypeUser::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\TypeUserEntity");
      return $c;
    },[]);
  }else
    return $arr;
    }else
    return false;
  }
    public static function getTypeUserBy($sqlF,$key,$value,$filter=null){
      $arr=TypeUser::getTypeUserBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\TypeUserEntity");
        return $c;
      },[]);
    }else return $arr;
    }else{
      return false;
    }
      }
 }