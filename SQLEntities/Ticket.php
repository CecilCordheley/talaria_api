<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
 class Ticket{
    private $attr=["idTicket"=>'',"uuidTicket"=>'',"contentTicket"=>'',"dateTicket"=>'',"objetTicket"=>'',"prioriteTicket"=>'',"dataticket"=>'',"responsable"=>'',"Auteur"=>'',"service"=>'',"typeTicket"=>''];
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
    public static function  add(SQLFactory $sqlF,Ticket &$item,$callBack=null){
     $return= $sqlF->addItem($item->getArray(),"ticket");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->idTicket=$sqlF->lastInsertId("ticket");
      if($callBack!=null){
        call_user_func($callBack,$item);
      }
      return true;
    }
    }
    public static function  update(SQLFactory $sqlF,Ticket $item,$callBack=null){
      $return=$sqlF->updateItem($item->getArray(),"ticket");
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
    public static function  del(SQLFactory $sqlF,Ticket $item){
      $sqlF->deleteItem($item->idTicket,"ticket");
    }
    public static function getAll($sqlF){
      $query=$sqlF->execQuery("SELECT * FROM ticket");
      $return=[];
      foreach($query as $element){
      $entity=new Ticket();
         $entity->idTicket=$element["idTicket"];
$entity->uuidTicket=$element["uuidTicket"];
$entity->contentTicket=$element["contentTicket"];
$entity->dateTicket=$element["dateTicket"];
$entity->objetTicket=$element["objetTicket"];
$entity->prioriteTicket=$element["prioriteTicket"];
$entity->dataticket=$element["dataticket"];
$entity->responsable=$element["responsable"];
$entity->Auteur=$element["Auteur"];
$entity->service=$element["service"];
$entity->typeTicket=$element["typeTicket"];
      $return[]=$entity;
      }
     return (count($return)>1)?$return:$return[0];
    }
    public static function getTicketBy($sqlF,$key,$value,$filter=null){
      $query=$sqlF->prepareQuery("SELECT * FROM ticket WHERE $key=:val",$key,$value);
      $return=[];
      foreach($query as $element){
      $entity=new Ticket();
         $entity->idTicket=$element["idTicket"];
$entity->uuidTicket=$element["uuidTicket"];
$entity->contentTicket=$element["contentTicket"];
$entity->dateTicket=$element["dateTicket"];
$entity->objetTicket=$element["objetTicket"];
$entity->prioriteTicket=$element["prioriteTicket"];
$entity->dataticket=$element["dataticket"];
$entity->responsable=$element["responsable"];
$entity->Auteur=$element["Auteur"];
$entity->service=$element["service"];
$entity->typeTicket=$element["typeTicket"];
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