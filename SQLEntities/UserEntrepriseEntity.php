<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\UserEntreprise;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `UserEntreprise`.
* Hérite de `UserEntreprise`. Ajoutez ici vos propres méthodes.
*/
class UserEntrepriseEntity extends UserEntreprise
{
   // Ajoutez vos méthodes ici
    public static function delByUser(SQLFactory $sqlF,$idUser){
        return $sqlF->execQuery("DELETE FROM user_entreprise WHERE user_idUser=$idUser");
    }
   public static function getAll($sqlF){
    $arr=UserEntreprise::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(UserEntreprise::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\UserEntrepriseEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\UserEntrepriseEntity");
    }else
    return false;
  }
    public static function getUserEntrepriseBy($sqlF,$key,$value,$filter=null){
      $arr=UserEntreprise::getUserEntrepriseBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\UserEntrepriseEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\UserEntrepriseEntity");
    }else{
      return false;
    }
      }
 }