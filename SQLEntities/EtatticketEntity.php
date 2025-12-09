<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Etatticket;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Etatticket`.
* Hérite de `Etatticket`. Ajoutez ici vos propres méthodes.
*/
class EtatticketEntity extends Etatticket
{
   // Ajoutez vos méthodes ici

   public static function getAll($sqlF){
    $arr=Etatticket::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Etatticket::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\EtatticketEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\EtatticketEntity");
    }else
    return false;
  }
    public static function getEtatticketBy($sqlF,$key,$value,$filter=null){
      $arr=Etatticket::getEtatticketBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\EtatticketEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\EtatticketEntity");
    }else{
      return false;
    }
      }
 }