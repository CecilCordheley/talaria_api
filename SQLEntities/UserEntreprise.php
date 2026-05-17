<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use Exception;
class UserEntreprise
{
  private $attr = ["user_idUser" => '', "entreprise_idEntreprise" => '', 'is_creator' => 0];
  public function __set($name, $value)
  {
    if (array_key_exists($name, $this->attr)) {
      $this->attr[$name] = $value;
    } else {
      throw new Exception("Propriété non définie : $name");
    }
  }
  public function getArray()
  {
    return $this->attr;
  }
  public function __get($name)
  {
    if (array_key_exists($name, $this->attr)) {
      return $this->attr[$name];
    } else {
      throw new Exception("Propriété non définie : $name");
    }
  }
  public static function add(SQLFactory $sqlF, UserEntreprise &$item, $callBack = null)
  {
    $return = $sqlF->addItem($item->getArray(), "user_entreprise");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      $item->user_idUser = $sqlF->lastInsertId("user_entreprise");
      if ($callBack != null) {
        call_user_func($callBack, $item);
      }
      return true;
    }
  }
  public static function update(SQLFactory $sqlF, UserEntreprise $item, $callBack = null)
  {
    $return = $sqlF->updateItem($item->getArray(), "user_entreprise");
    if (gettype($return) === "string" && strpos($return, "Error") !== -1) {
      echo "<pre>$return</pre>";
      return false;
    } else {
      if ($callBack != null) {
        call_user_func($callBack, $item);
      }
      return true;
    }
  }
  public static function del(SQLFactory $sqlF, UserEntreprise $item)
  {
    $sqlF->deleteItem($item->user_idUser, "user_entreprise");
  }
  public static function getAll($sqlF)
  {
    $query = $sqlF->execQuery("SELECT * FROM user_entreprise");
    $return = [];
    foreach ($query as $element) {
      $entity = new UserEntreprise();
      $entity->user_idUser = $element["user_idUser"];
      $entity->entreprise_idEntreprise = $element["entreprise_idEntreprise"];
      $entity->is_creator = $element["is_creator"];
      $return[] = $entity;
    }
    return (count($return) > 1) ? $return : $return[0];
  }
  public static function getUserEntrepriseBy($sqlF, $key, $value, $filter = null)
  {
    $query = $sqlF->prepareQuery("SELECT * FROM user_entreprise WHERE $key=:val", $key, $value);
    $return = [];
    foreach ($query as $element) {
      $entity = new UserEntreprise();
      $entity->user_idUser = $element["user_idUser"];
      $entity->entreprise_idEntreprise = $element["entreprise_idEntreprise"];
      $entity->is_creator = $element["is_creator"];
      $return[] = $entity;
    }
    if ($filter != null && count($return) > 0) {
      $return = array_filter($return, $filter);
    }
    if (count($return))
      return (count($return) > 1) ? $return : $return[0];
    else
      return false;
  }
}