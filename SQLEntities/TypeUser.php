<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class TypeUser{
    private $attr=["idTypeUser"=>'',"libTypeUser"=>'',"refTypeUser"=>''];
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
    public static function  add(SQLFactory $sqlF,TypeUser &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"type_user");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idTypeUser=$sqlF->lastInsertId("type_user");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,TypeUser $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"type_user");
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
    public static function  del(SQLFactory $sqlF,TypeUser $item){
      $sqlF->deleteItem($item->idTypeUser,"type_user");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM type_user");
      $return=[];
      foreach($query as $element){
      $entity=new TypeUser();
         $entity->idTypeUser=$element["idTypeUser"];
$entity->libTypeUser=$element["libTypeUser"];
$entity->refTypeUser=$element["refTypeUser"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getTypeUserBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM type_user WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new TypeUser();
         $entity->idTypeUser=$element["idTypeUser"];
$entity->libTypeUser=$element["libTypeUser"];
$entity->refTypeUser=$element["refTypeUser"];
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