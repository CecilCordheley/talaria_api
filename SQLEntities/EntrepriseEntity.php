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
  public function getUsers(SQLFactory $sqlF){
    //get user from user_entreprise
    $user_e=UserEntrepriseEntity::getUserEntrepriseBy($sqlF,"entreprise_idEntreprise",$this->idEntreprise);
    if(is_array($user_e))
    return array_reduce($user_e,function($c,$i) use ($sqlF){
      $u=UserEntity::getUserBy($sqlF,"idUser",$i->user_idUser);
      $c[]=$u;
      return $c;
    },[]);
    else
      return UserEntity::getUserBy($sqlF,"idUser",$user_e->user_idUser);
     
  }
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