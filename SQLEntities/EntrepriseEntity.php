<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Entreprise;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Entreprise`.
* Hérite de `Entreprise`. Ajoutez ici vos propres méthodes.
*/
class EntrepriseEntity extends Entreprise
{
   /**
    * Retourne tout les services de l'entreprise
    * @param mixed $sqlF
    * @return void
    */
   public function getServices($sqlF){
      return ServiceEntity::getServiceBy($sqlF,"Entreprise",$this->idEntreprise);
      
   }

   public static function getAll($sqlF){
    $arr=Entreprise::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Entreprise::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\EntrepriseEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\EntrepriseEntity");
    }else
    return false;
  }
    public static function getEntrepriseBy($sqlF,$key,$value,$filter=null){
      $arr=Entreprise::getEntrepriseBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\EntrepriseEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\EntrepriseEntity");
    }else{
      return false;
    }
      }
 }