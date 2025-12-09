<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class User{
    private $attr=["idUser"=>'',"nomUser"=>'',"prenomUser"=>'',"mailUser"=>'',"mdpUser"=>'',"validiteMdp"=>'',"uuidUser"=>'',"dataAgent"=>'',"service_idService"=>'',"typeUser"=>''];
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
    public static function  add(SQLFactory $sqlF,User &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"user");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idUser=$sqlF->lastInsertId("user");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,User $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"user");
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
    public static function  del(SQLFactory $sqlF,User $item){
      $sqlF->deleteItem($item->idUser,"user");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM user");
      $return=[];
      foreach($query as $element){
      $entity=new User();
         $entity->idUser=$element["idUser"];
$entity->nomUser=$element["nomUser"];
$entity->prenomUser=$element["prenomUser"];
$entity->mailUser=$element["mailUser"];
$entity->mdpUser=$element["mdpUser"];
$entity->validiteMdp=$element["validiteMdp"];
$entity->uuidUser=$element["uuidUser"];
$entity->dataAgent=$element["dataAgent"];
$entity->service_idService=$element["service_idService"];
$entity->typeUser=$element["typeUser"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getUserBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM user WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new User();
         $entity->idUser=$element["idUser"];
$entity->nomUser=$element["nomUser"];
$entity->prenomUser=$element["prenomUser"];
$entity->mailUser=$element["mailUser"];
$entity->mdpUser=$element["mdpUser"];
$entity->validiteMdp=$element["validiteMdp"];
$entity->uuidUser=$element["uuidUser"];
$entity->dataAgent=$element["dataAgent"];
$entity->service_idService=$element["service_idService"];
$entity->typeUser=$element["typeUser"];
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