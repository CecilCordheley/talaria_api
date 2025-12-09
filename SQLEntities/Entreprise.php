<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class Entreprise{
    private $attr=["idEntreprise"=>'',"nomEntreprise"=>'',"siretEntreprise"=>'',"adresseEntrerprise"=>'',"cpEntreprise"=>'',"villeEntreprise"=>'',"telEntreprise"=>'',"mailEntreprise"=>'',"typeEntreprise"=>'',"dataEntreprise"=>'',"created_At"=>'',"update_At"=>''];
    public function __set($name,$value){
      if (array_key_exists($name, $this->attr)) {
         $this->attr[$name]=$value;
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public function getArray(){
      return $this->attr;
    }
    public function __get($name){
      if (array_key_exists($name, $this->attr)) {
         return $this->attr[$name];
     } else {
         throw new Exception("Propriété non définie : $name");
     }
    }
    public static function  add(SQLFactory $sqlF,Entreprise &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"entreprise");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idEntreprise=$sqlF->lastInsertId("entreprise");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,Entreprise $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"entreprise");
      if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
        echo "<pre>$return</pre>";
        return false;
      } else {
        if($callBack!=null){
          call_user_func($callBack,$item);
        }
        return true;
      }
    }
    public static function  del(SQLFactory $sqlF,Entreprise $item){
      $sqlF->deleteItem($item->idEntreprise,"entreprise");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM entreprise");
      $return=[];
      foreach($query as $element){
      $entity=new Entreprise();
         $entity->idEntreprise=$element["idEntreprise"];
$entity->nomEntreprise=$element["nomEntreprise"];
$entity->siretEntreprise=$element["siretEntreprise"];
$entity->adresseEntrerprise=$element["adresseEntrerprise"];
$entity->cpEntreprise=$element["cpEntreprise"];
$entity->villeEntreprise=$element["villeEntreprise"];
$entity->telEntreprise=$element["telEntreprise"];
$entity->mailEntreprise=$element["mailEntreprise"];
$entity->typeEntreprise=$element["typeEntreprise"];
$entity->dataEntreprise=$element["dataEntreprise"];
$entity->created_At=$element["created_At"];
$entity->update_At=$element["update_At"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getEntrepriseBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM entreprise WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new Entreprise();
         $entity->idEntreprise=$element["idEntreprise"];
$entity->nomEntreprise=$element["nomEntreprise"];
$entity->siretEntreprise=$element["siretEntreprise"];
$entity->adresseEntrerprise=$element["adresseEntrerprise"];
$entity->cpEntreprise=$element["cpEntreprise"];
$entity->villeEntreprise=$element["villeEntreprise"];
$entity->telEntreprise=$element["telEntreprise"];
$entity->mailEntreprise=$element["mailEntreprise"];
$entity->typeEntreprise=$element["typeEntreprise"];
$entity->dataEntreprise=$element["dataEntreprise"];
$entity->created_At=$element["created_At"];
$entity->update_At=$element["update_At"];
      $return[]=$entity;
      }
      if($filter!=null && count($return)>0){
        $return = array_filter($return,$filter);
      }
      if(count($return))
      return (count($return) > 1) ? $return : $return[0];
    else
      return false;
    }
 }