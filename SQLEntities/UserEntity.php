<?php
namespace SQLEntities;

use DateTime;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\DbTokenManager;
use SQLEntities\User;
use SQLEntities\ServiceEntity;

use Exception;



/**
 * Class personnalisée pour la table `User`.
 * Hérite de `User`. Ajoutez ici vos propres méthodes.
 */
class UserEntity extends User
{
  // Ajoutez vos méthodes ici

  public function associateService($sqlF, ServiceEntity $service)
  {
    $this->service_idService = $service->idService;
    return UserEntity::update($sqlF, $this);
  }
  /**
   * Retourne l'utilisateur en fonction du mail et du mot de passe
   * @param string $mail
   * @param string $mdp
   * @param callable $callback
   * @return void
   */
  public static function connexion(SQLFactory $sqlF, string $mail, string $mdp, callable $callback)
  {
    $users = self::getUserBy($sqlF, 'mailUser', $mail);

    if (!$users) {
      throw new Exception("Utilisateur introuvable");
    }
    if ($users->mdpUser == "") {
      throw new Exception("First connexion");
    }
    $user = $users;
    $crypto = new Cryptographer();
    $hash_password = $crypto->hashString($mdp);
    if ($user->mdpUser != $hash_password) {
      throw new Exception("Mot de passe incorect");
    }

    if ($callback != null) {
      call_user_func($callback, $user);
    }
    $sqlF = new SQLFactory(null, "../include/config.ini", "security");
    DbTokenManager::setPDO($sqlF->getPdo());
    $role = ["admin", "agent", "dev", "manager"][($user->typeUser * 1) - 1];
    $token = DbTokenManager::generate($user->uuidUser, $role);
    $delegate = DbTokenManager::getDelegate($user->uuidUser);
    $now = date("Y-m-d");

    if ($delegate == false)
      return [
        "token" => $token,
        "role" => $role,
        "validity" => Main::DateCompare($user->validiteMdp, $now),
        "user_id" => $user->uuidUser
      ];
    else {

      return [
        "token" => $token,
        "delegate" => $delegate,
        "role" => $role,
        "validity" => Main::DateCompare($user->validiteMdp, $now),
        "user_id" => $user->uuidUser
      ];
    }
  }
  public function getEntreprise(SQLFactory $sqlF)
  {
    $e = UserEntrepriseEntity::getUserEntrepriseBy($sqlF, "user_idUser", $this->idUser);
    if($e){
      if(gettype($e)=="array"){
        return array_reduce($e,function($c,$e) use ($sqlF){
          $item=EntrepriseEntity::getEntrepriseBy($sqlF,"idEntreprise",$e->entreprise_idEntreprise);
          $c[]=$item->getArray();
          return $c;
        },[]);
      }else{
        $item=EntrepriseEntity::getEntrepriseBy($sqlF,"idEntreprise",$e->entreprise_idEntreprise);
        return [$item->getArray()];
      }
    }else{
      return false;
    }
    return $return;
  }
  public static function update(SQLFactory $sqlF, User $item, $callBack = null)
  {
    $data = $item->dataAgent;

    // Si dataAgent n'est pas déjà un tableau, on tente de le décoder
    if (!is_array($data)) {
      // Remplace les simples quotes et corrige les clés non entre guillemets
      $data = stripslashes($data); // <-- retire les antislashs inutiles
      $data = preg_replace("/'/", '"', $data);
      $data = preg_replace('/([{,])\s*([a-zA-Z0-9_]+)\s*:/', '$1"$2":', $data);

      $decoded = json_decode($data, true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception(
          "From userEntity — Le JSON passé dans dataAgent est invalide : "
          . json_last_error_msg()
          . "\nDonnées reçues : " . print_r($data, true)
        );
      }
    } else {
      $decoded = $data;
    }

    // Ré-encode proprement
    $newData = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $item->dataAgent = str_replace('"', "\\\"", $newData);

    return parent::update($sqlF, $item, $callBack);
  }

  public static function getAll($sqlF)
  {
    $arr = User::getAll($sqlF);
    if ($arr) {
      if (gettype($arr) == "array") {
        return array_reduce(User::getAll($sqlF), function ($c, $e) {
          $c[] = Main::fixObject($e, "SQLEntities\UserEntity");
          return $c;
        }, []);
      } else
        return Main::fixObject($arr, "SQLEntities\UserEntity");
    } else
      return false;
  }
  public static function getUserBy($sqlF, $key, $value, $filter = null)
  {
    $arr = User::getUserBy($sqlF, $key, $value, $filter);
    if ($arr) {
      if (gettype($arr) == "array") {
        return array_reduce($arr, function ($c, $e) {
          $c[] = Main::fixObject($e, "SQLEntities\UserEntity");
          return $c;
        }, []);
      } else
        return Main::fixObject($arr, "SQLEntities\UserEntity");
    } else {
      return false;
    }
  }
}