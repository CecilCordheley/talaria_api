<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Service;
use vendor\easyFrameWork\Core\Main;
use SQLEntities\UserEntity;
use SQLEntities\TicketEntity;
use Exception;
use DateTime;
/**
 * Class personnalisée pour la table `Service`.
 * Hérite de `Service`. Ajoutez ici vos propres méthodes.
 */
class ServiceEntity extends Service
{
  /**
   * Retourne tout les utilistateurs du service
   * @param \vendor\easyFrameWork\Core\Master\SQLFactory $sqlF
   */
  public function getUsers(SQLFactory $sqlF)
  {
    return UserEntity::getUserBy($sqlF, "service_idService", $this->idService);
  }
  public function generateForwardAgent($sqlF)
  {
    try {
      $agent = new UserEntity();
      $agent->nomUser = "TRANSITIONAL";
      $agent->prenomUser = "FORWARD " . $this->uuidService;
      $agent->mailUser = "TRANSIFORWARD." . $this->uuidService . "@nomail.com";
      $agent->mdpUser = " **** ";
      $agent->uuidUser = "TRANSI-" . $this->idService;
      $agent->typeUser = 5;
      $agent->service_idService = $this->idService;
      $arrive = date("Y-m-d");
      $date = new DateTime($arrive);
      // Ajout de 1 mois
      $date->modify('+12 months');
      //durée de validité du mot de passe
      $valid = $date->format('Y-m-d');
      $agent->validiteMdp = $valid;
      $agent->dataAgent = "{}";
      $return = UserEntity::add($sqlF, $agent);
      if ($return) {
        return true;
      } else {
        echo "Une erreur est survenue";
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
  public function getTicketFrom($sqlF)
  {
    $return = $sqlF->execQuery("SELECT idTicket FROM `ticket` t
INNER JOIN `user` u on t.Auteur=u.idUser
WHERE u.service_idService=" . $this->idService);
    $a = [];
    // var_dump($return);
    foreach ($return as $ticket) {
      $t = TicketEntity::getTicketBy($sqlF, "IdTicket", $ticket["idTicket"]);
      $a[] = $t;
    }
    return $a;
  }
  /**
   * retourne tout les ticket destiné au service
   * @param \vendor\easyFrameWork\Core\Master\SQLFactory $sqlF
   */
  public function getTickets(SQLFactory $sqlF)
  {
    $t = TicketEntity::getTicketBy($sqlF, "service", $this->idService);
    if ($t == false) {
      return false;
    }
    $return = is_array($t) ? $t : [$t];
    return $return;
  }
  public static function getAll($sqlF)
  {
    $arr = Service::getAll($sqlF);
    if ($arr) {
      if (gettype($arr) == "array") {
        return array_reduce(Service::getAll($sqlF), function ($c, $e) {
          $c[] = Main::fixObject($e, "SQLEntities\ServiceEntity");
          return $c;
        }, []);
      } else
        return Main::fixObject($arr, "SQLEntities\ServiceEntity");
    } else
      return false;
  }
  public static function getServiceBy($sqlF, $key, $value, $filter = null)
  {
    $arr = Service::getServiceBy($sqlF, $key, $value, $filter);
    if ($arr) {
      if (gettype($arr) == "array") {
        return array_reduce($arr, function ($c, $e) {
          $c[] = Main::fixObject($e, "SQLEntities\ServiceEntity");
          return $c;
        }, []);
      } else
        return Main::fixObject($arr, "SQLEntities\ServiceEntity");
    } else {
      return false;
    }
  }
}