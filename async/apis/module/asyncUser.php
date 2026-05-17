<?php
namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\Entreprise;
use SQLEntities\EntrepriseEntity;
use SQLEntities\ServiceEntity;
use SQLEntities\TicketEntity;
use SQLEntities\UserEntity;
use SQLEntities\UserEntreprise;
use SQLEntities\UserEntrepriseEntity;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\DbTokenManager;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgentV2;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;

/**
 * Ensemble des fonctions asynchrone relative aux utilisateur
 */
abstract class AsyncUser
{
    /**
     * retourne une Instance de SQL Factory
     */
    private static function getSQLFactory(): SQLFactory
    {
        return new SQLFactory(null, "../include/config.ini");
    }
    public static function checkValidToken()
    {
        try {
            return MiddleAgentV2::checkToken();
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    private static function checkToken(mixed $roles)
    {
        $userData = MiddleAgentV2::checkRole($roles);
        //  var_dump($userData);
        $user_required = UserEntity::getUserBy(self::getSQLFactory(), "uuidUser", $userData["user_id"]);
        if ($user_required == false) {
            echo json_encode(["result" => "error", "message" => "no user finded with current token"]);
            exit();
        }
    }
    private static function selectUser(SQLFactory $sqlF, string $uuidUser): UserEntity
    {
        $user = UserEntity::getUserBy($sqlF, "uuidUser", $uuidUser);
        if ($user === false) {
            echo json_encode(["result" => "error", "message" => "user doesn't exist !"]);
            exit();
        }
        return $user;
    }
    public static function updateData($idUser, $key, $value)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager"]);
            $user = self::selectUser($sqlF, $idUser);
            $currentData = json_decode($user->dataAgent, true);
            if (!is_array($currentData)) {
                $currentData = [];
            }
            if ($value != "")
                $currentData[$key] = $value;
            else
                unset($currentData[$key]);
            $newData = json_encode($currentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $user->dataAgent = str_replace('"', "\\\"", $newData);
            $return = UserEntity::update($sqlF, $user);
            if ($return) {
                if ($value != "")
                    return "data $key with $value updated successful";
                else
                    return "data $key has been removed";
            } else {
                echo json_encode(["result" => "error", "message" => "data $key with $value updated failed"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function updatePassWord($idUser, $newPassWord)
    {
        try {
            $sqlF = self::getSQLFactory();
            //   self::checkToken(["admin","manager",'agent','dev']);
            //Get User
            $user = UserEntity::getUserBy($sqlF, "uuidUser", $idUser);
            if ($user === false) {
                echo json_encode(["result" => "error", "message" => "user doesn't exist !"]);
                exit();
            }
            $crypto = new Cryptographer();
            $user->mdpUser = $crypto->hashString($newPassWord);
            $arrive = date("Y-m-d");
            $date = new DateTime($arrive);
            // Ajout de 1 mois
            $date->modify('+1 months');
            //durée de validité du mot de passe
            $valid = $date->format('Y-m-d');
            $user->validiteMdp = $valid;
            if (UserEntity::update($sqlF, $user)) {
                return "user updated !";
            } else {
                echo json_encode(["result" => "error", "message" => "user update failed !!"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function updateUser($idUser)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager"]);
            //Get User
            $user = UserEntity::getUserBy($sqlF, "uuidUser", $idUser);
            if ($user === false) {
                echo json_encode(["result" => "error", "message" => "user doesn't exist !"]);
                exit();
            }
            if ($user->typeUser == 1) {
                echo json_encode(["result" => "error", "message" => "can't update this user !"]);
                exit();
            }
            $jsonData = isset($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);

            $user->nomUser = isset($jsonData["nom"]) ? $jsonData["nom"] : $user->nomUser;
            $user->prenomUser = isset($jsonData["prenom"]) ? $jsonData["prenom"] : $user->prenomUser;
            $user->mailUser = isset($jsonData["mail"]) ? $jsonData["mail"] : $user->mailUser;
            $data = isset($jsonData["data"]) ? $jsonData["data"] : $user->dataAgent;
            if (!is_array($data)) {
                // Nettoyage basique du JSON
                $data = preg_replace("/'/", '"', $data); // remplace les simples quotes par doubles
                $data = preg_replace('/([{,])\s*([a-zA-Z0-9_]+)\s*:/', '$1"$2":', $data); // ajoute des quotes autour des clés si manquantes

                $decoded = json_decode($data, true);
            }
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("$decoded Le JSON passé dans dataAgent est invalide : " . json_last_error_msg());
            }
            $newData = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $user->dataAgent = str_replace('"', "\\\"", $newData);
            $user->service_idService = isset($jsonData["service"])&&!empty($jsonData["service"]) ? ServiceEntity::getServiceBy($sqlF, "idService", $jsonData["service"])->idService : $user->service_idService;
            if (isset($jsonData["Entreprise"])) {
                $ent = EntrepriseEntity::getEntrepriseBy($sqlF, "siretEntreprise", $jsonData["Entreprise"]);
                if ($ent != false) {
                    UserEntrepriseEntity::delByUser($sqlF, $user->idUser);
                    $userEnt = new UserEntrepriseEntity();
                    $userEnt->entreprise_idEntreprise = $ent->idEntreprise;
                    $userEnt->user_idUser = $user->idUser;
                    if (!UserEntrepriseEntity::add($sqlF, $userEnt)) {
                        echo json_encode(["result" => "error", "message" => "association with entreprise failed"]);
                        exit();
                    }
                }
            }
            if (UserEntity::update($sqlF, $user)) {
                return "user updated !";
            } else {
                echo json_encode(["result" => "error", "message" => "user update failed !!"]);
                exit();
            }

        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    /**
     * associe un utilisateur à un service
     * @param string $user UUID de l'utilisateur
     * @param string $service numéro reférence du service
     * @return void
     */
    public static function associateService(string $user, string $service)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager"]);
            //obtenir le service 
            $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $service);
            if ($service == false) {
                echo json_encode(["result" => "error", "message" => "Service doesn't exist"]);
                exit();
            }
            //obtenir l'utilisateur
            $user = UserEntity::getUserBy($sqlF, "uuidUser", $user);

            if ($user->associateService($sqlF, $service)) {
                return "user is associate to service";
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function delUser($idUser)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager"]);
            //Get User
            $user = UserEntity::getUserBy($sqlF, "uuidUser", $idUser);
            if ($user === false) {
                echo json_encode(["result" => "error", "message" => "user doesn't exist !"]);
                exit();
            }
            if ($user->typeUser == 1) {
                echo json_encode(["result" => "error", "message" => "can't delete this user !"]);
                exit();
            }
            //vérifie si l'utilisateur est auteur ou responsable d'un ticket, si oui, interdit la suppression
            if (TicketEntity::isAuthorOrResponsable($sqlF, $user->idUser)) {
                echo json_encode(["result" => "error", "message" => "can't delete this user because it's author or responsable of a ticket !"]);
                exit();
            }
            $user_entr = UserEntrepriseEntity::getUserEntrepriseBy($sqlF, "user_idUser", $user->idUser);
            // var_dump($user_entr);
            if ($user_entr != false) {
                $user_delbyEntr = UserEntrepriseEntity::delByUser($sqlF, $user->idUser);
                // var_dump($user_delbyEntr);
                if ($user_delbyEntr !== false) {
                    if (UserEntity::del($sqlF, $user) !== false) {
                        return "user deleted !";
                    } else {
                        echo json_encode(["result" => "error", "message" => "user delete failed !!"]);
                        exit();
                    }

                } else {
                    echo json_encode(["result" => "error", "message" => "user can't be deleted !!"]);
                    exit();
                }
            } else {
                if (UserEntity::del($sqlF, $user)) {
                    return "user has no entreprise associated deleted !";
                } else {
                    echo json_encode(["result" => "error", "message" => "user delete failed !!"]);
                    exit();
                }
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    /**
     * Créé un utilisateur
     * @param int $type
     */
    public static function createUser($type)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', 'manager', "dev"]);
            //Récupérer les données en POST
            $jsonData = isset($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
            $user = new UserEntity();
            $user->nomUser = $jsonData["nom"] ?? "nom_user";
            $user->prenomUser = $jsonData["prenom"] ?? "prenom_user";
            if (!isset($jsonData["mail"])) {
                echo json_encode(["result" => "error", "message" => "no mail param"]);
                exit();
            }
            $user->mailUser = $jsonData["mail"];
            if (!isset($jsonData["mdp"])) {
                echo json_encode(["result" => "error", "message" => "no mdp param"]);
                exit();
            }
            $crypto = new Cryptographer();
            $user->mdpUser = $crypto->hashString($jsonData["mdp"]);
            $user->typeUser = $type;
            $user->uuidUser = uniqid();
            $user->dataAgent = "{}";
            if (is_numeric($jsonData["service"] ?? "")) {
                $service = ServiceEntity::getServiceBy($sqlF, "idService", $jsonData["service"]);
            } else {
                $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $jsonData["service"] ?? "");
            }
            if ($service == false) {
                echo json_encode(["result" => "error", "message" => "Service doesn't exist"]);
                exit();
            }
            $user->service_idService = $service->idService;
            $arrive = date("Y-m-d");
            $date = new DateTime($arrive);
            // Ajout de 1 mois
            $date->modify('+1 months');
            //durée de validité du mot de passe
            $valid = $date->format('Y-m-d');
            $user->validiteMdp = $valid;
            $return = UserEntity::add($sqlF, $user);
            if ($return) {
                if (isset($jsonData["Entreprise"])) {
                    if (is_numeric($jsonData["Entreprise"])) {
                        $ent = EntrepriseEntity::getEntrepriseBy($sqlF, "idEntreprise", $jsonData["Entreprise"]);
                    } else {
                        $ent = EntrepriseEntity::getEntrepriseBy($sqlF, "siretEntreprise", $jsonData["Entreprise"]);
                    }
                    if ($ent != false) {
                        $userEnt = new UserEntreprise();
                        $userEnt->entreprise_idEntreprise = $ent->idEntreprise;
                        $userEnt->user_idUser = $user->idUser;
                        if (UserEntreprise::add($sqlF, $userEnt)) {
                            return $user->uuidUser;
                        } else {
                            echo json_encode(["result" => "error", "message" => "user created but association with entreprise failed"]);
                            exit();
                        }
                    } else {
                        echo json_encode(["result" => "error", "message" => "user created but entreprise doesn't exist"]);
                        exit();
                    }
                } else {
                    return $user->uuidUser;
                }

            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite dans la création"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function getUser($id = null)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager", "agent", "dev"]);
            if ($id == null) {
                $return = UserEntity::getAll($sqlF);
                if (is_array($return))
                    return array_reduce($return, function ($car, $el) use ($sqlF) {
                        $arr = $el->getArray();
                        $serv = ServiceEntity::getServiceBy($sqlF, "idService", $el->service_idService);
                        $arr["Entreprise"] = $el->getEntreprise($sqlF);
                        /*  if($serv->Entreprise!=null){
                              $ent=EntrepriseEntity::getEntrepriseBy($sqlF,"idEntreprise",$serv->Entreprise);
                              if($ent!=false)
                                  $arr["Entreprise"]=$ent->getArray();
                              else
                                  $arr["Entreprise"]=false;
                          }else
                              $arr["Entreprise"]=false;*/
                        if ($serv != false)
                            $arr["uuidService"] = $serv->uuidService;
                        // unset($arr["idUser"]);
                        $car[] = $arr;
                        return $car;
                    }, []);
                else {
                    $arr = $return->getArray();
                    $arr["uuidService"] = ServiceEntity::getServiceBy($sqlF, "idService", $return->service_idService)->uuidService ?? "";
                    //   unset($arr["idUser"]);
                    return $arr;
                }
            } else {
                if (is_numeric($id)) {
                    $return = UserEntity::getUserBy($sqlF, "idUser", $id);
                } else
                    $return = UserEntity::getUserBy($sqlF, "uuidUser", $id);
                $arr = $return->getArray();
                $arr["uuidService"] = ServiceEntity::getServiceBy($sqlF, "idService", $return->service_idService)->uuidService ?? "";
                $arr["Entreprise"] = $return->getEntreprise($sqlF);
                if ($arr["uuidService"] != "") {
                    $serv = ServiceEntity::getServiceBy($sqlF, "idService", $return->service_idService);

                    //   var_dump($serv);
                    /*  if($serv->Entreprise!=""){
                          $ent=EntrepriseEntity::getEntrepriseBy($sqlF,"idEntreprise",$serv->Entreprise);
                          if($ent!=false)
                              $arr["Entreprise"]=$ent->getArray();
                          else
                              $arr["Entreprise"]=false;
                      }else
                          $arr["Entreprise"]=false;
                       }else{
                          $arr["Entrerpise"]=false;*/
                }
                return $arr;
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }

    }
    public static function connexion($mail, $mdp)
    {
        try {
            $session_manager = new SessionManager;
            $entr = "";
            $return = UserEntity::connexion(
                self::getSQLFactory(),
                $mail,
                $mdp,
                function ($user) use ($session_manager) {
                    $session_manager->set("user", $user);
                }
            );

            if ($return != false) {

                $log = new HistoryLog("../include/connexion.log");
                $log->addEntry($return["user_id"] . "- connexion");
                $log->commit();
                // DbTokenManager::generate($return["user_id"],$return["role"],[]);
                $session_manager->set("isConnect", "1");
                return $return;
            }
            return ["status" => "error", "message" => "not a valid mail or pwd"];
        } catch (Exception $exception) {
            echo json_encode(["status" => "error", "message" => $exception->getMessage()]);
            exit();
        }
    }
}