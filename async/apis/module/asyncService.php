<?php
namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\EntrepriseEntity;
use SQLEntities\Service;
use SQLEntities\ServiceEntity;
use SQLEntities\TicketEntity;
use SQLEntities\TypeticketEntity;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\HistoryLog;
use Vendor\EasyFrameWork\Core\Master\MiddleAgentV2;
use vendor\easyFrameWork\Core\Master\SessionManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;

/**
 * Ensemble des fonctions asynchrone relative aux services
 */
abstract class asyncService
{
    /**
     * retourne une Instance de SQL Factory
     */
    private static function getSQLFactory(): SQLFactory
    {
        return new SQLFactory(null, "../include/config.ini");
    }
    public static function getUsers($idService)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', 'manager', 'agent']);
            //Récupérer les données en POST
            $service = ServiceEntity::getServiceBy($sqlF, "idService", $idService);
            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "service $idService n'existe pas"]);
                exit();
            }
            //      var_dump($service);
            $return = $service->getUsers($sqlF);

            if ($return) {
                if (!is_array($return)) {
                    $return = [$return];
                }
                $a = array_reduce($return, function ($c, $e) {
                    $u = $e->getArray();
                    unset($u["idUser"]);
                    unset($u["mdpUser"]);
                    unset($u["validateMdp"]);
                    $c[] = $u;
                    return $c;
                }, []);


                return ["service" => $service->uuidService, "user" => $a];
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function getTicketsTo($idService)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', 'manager', 'agent']);
            //Récupérer les données en POST
            if (is_numeric($idService)) {
                $service = ServiceEntity::getServiceBy($sqlF, "idService", $idService);
            } else {
                $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $idService);
            }

            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "service $idService n'existe pas"]);
                exit();
            }
            //      var_dump($service);
            $return = $service->getTickets($sqlF);
            if ($return !== false) {

                if (count($return) === 0) {
                    echo json_encode(["result" => "error", "message" => "Pas de ticket pour ce service"]);
                    exit();
                }
                return array_reduce($return, function ($c, $e) use (&$sqlF) {
                    $t = $e->getArray();
                    $auteur = UserEntity::getUserBy($sqlF, "idUser", $e->Auteur);
                    $serv = ServiceEntity::getServiceBy($sqlF, "idService", $e->service);
                    $t["Auteur"] = $auteur->uuidUser;
                    $t["service"] = $serv->uuidService;
                    $t["typeTicket"] = TypeticketEntity::getTypeTicketBy($sqlF, "idTypeTicket", $e->typeTicket)->refTypeTicket;
                    $i = 0;
                    $state = array_reduce($e->getEtats($sqlF), function ($c, $s) use (&$i, $sqlF) {
                        $c[$i] = $s->getArray();
                        $c[$i]["state"] = $s->getState($sqlF)->getArray();
                        $i++;
                        return $c;
                    }, []);
                    //seul les tickets avec un état différent de Created sont retourné
                    if ($state[0]["state"]["refEtatTicket"] != "CREA-G73FF") {
                        return $c;
                    }
                    $c[] = ["ticket" => $t, "state" => $state];
                    return $c;
                }, []);
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite lors de l'exécution de la requête"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function getTickets($idService)
    {
        try {
            $sqlF = self::getSQLFactory();
            $user_required = self::checkToken(['admin', 'manager', 'agent']);
            //get role
            $role=json_decode($user_required["scopes"],true);
            //Récupérer les données en POST
            if (is_numeric($idService))
                $service = ServiceEntity::getServiceBy($sqlF, "idService", $idService);
            else
                $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $idService);
            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "service $idService n'existe pas"]);
                exit();
            }
            //      var_dump($service);
            $return = $service->getTicketFrom($sqlF);

            if ($return) {
                return array_reduce($return, function ($c, $e) use (&$sqlF,$role) {
                    $t = $e->getArray();
                    $auteur = UserEntity::getUserBy($sqlF, "idUser", $e->Auteur);
                    $serv = ServiceEntity::getServiceBy($sqlF, "idService", $e->service);
                    $t["Auteur"] = $auteur->uuidUser;
                    $t["service"] = $serv->uuidService;
                    if($role["role"]==="manager"){
                        unset($t["entreprise_source"]);
                        unset($t["entreprise_cible"]);
                    }

                    $t["typeTicket"] = TypeticketEntity::getTypeTicketBy($sqlF, "idTypeTicket", $e->typeTicket)->refTypeTicket;
                    $i = 0;
                    $state = array_reduce($e->getEtats($sqlF), function ($c, $s) use (&$i, $sqlF) {
                        $c[$i] = $s->getArray();
                        $c[$i]["state"] = $s->getState($sqlF)->getArray();
                        $i++;
                        return $c;
                    }, []);
                    $c[] = ["ticket" => $t, "state" => $state];
                    return $c;
                }, []);
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite lors de l'exécution de la requête"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    private static function checkToken($roles)
    {
        $userData = MiddleAgentV2::checkRole($roles);
        $user_required = UserEntity::getUserBy(self::getSQLFactory(), "uuidUser", $userData["user_id"]);
        if ($user_required == false) {
            echo json_encode(["result" => "error", "message" => "no user finded with current token"]);
            exit();
        }
        return $userData;
    }
    public static function associateEntreprise($idService, $siretEntreprise)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin']);
            //Récupérer les données en POST
            $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $idService);
            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "service $idService n'existe pas"]);
                exit();
            }
            $entreprise = EntrepriseEntity::getEntrepriseBy($sqlF, "siretEntreprise", $siretEntreprise);
            if ($entreprise === false) {
                echo json_encode(["result" => "error", "message" => "Entreprise $entreprise n'existe pas"]);
                exit();
            }
            $service->Entreprise = $entreprise->idEntreprise;
            $return = ServiceEntity::update($sqlF, $service);
            if ($return) {
                return "service : $idService associé à Entrerprise : $siretEntreprise";
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite dans la mise à jour"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function toogleService($id)
    {
        try {

            $sqlF = self::getSQLFactory();
            self::checkToken(['admin']);
            //Récupérer les données en POST
            $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $id);
            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "Aucun service trouvé"]);
                exit();
            }
            $service->isActiv = $service->isActiv == "1" ? 0 : 1;
            $return = ServiceEntity::update($sqlF, $service);
            if ($return) {
                return "service state toogle";
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite dans la mise à jour"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function delService(string $id)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', "dev"]);
            $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $id);
            if ($service === false) {
                echo json_encode(["result" => "error", "message" => "Aucun service trouvé"]);
                exit();
            }
            //Vérifie si des utilisateur son associé au service
            if (UserEntity::getUserBy($sqlF, "service_idService", $service->idService) != false) {
                echo json_encode(["result" => "error", "message" => "Utilisateurs associés à ce service"]);
                exit();
            }
            //Vérifie si des ticket sont à destination du service
            if (TicketEntity::getTicketBy($sqlF, "service", $service->idService) != false) {
                echo json_encode(["result" => "error", "message" => "Tickets associés à ce service"]);
                exit();
            }
            return ServiceEntity::del($sqlF, $service);
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function updateService($id)
    {
        try {

            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', "dev"]);
            //Récupérer les données en POST
            $jsonData = isset($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
            $service = ServiceEntity::getServiceBy($sqlF, "uuidService", $id);
            $service->nomService = $jsonData["nom"] ?? $service->nomService;
            $service->descService = $jsonData["desc"] ?? $service->descService;
            /* $service->createAt=date("Y-m-d");
             $service->isActiv=1;
             $service->uuidService=uniqid();*/
            $service->Entreprise = $jsonData["entreprise"] ?? $service->Entreprise;
            $return = ServiceEntity::update($sqlF, $service);
            if ($return) {
                return "data updated !";
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite dans la mise à jour"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function createService()
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', "dev"]);
            //Récupérer les données en POST
            $jsonData = isset($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);
            $service = new ServiceEntity;
            $service->nomService = $jsonData["nom"] ?? "nom_service";
            $service->descService = $jsonData["desc"] ?? "desc_service";
            $service->createAt = date("Y-m-d");
            $service->isActiv = 1;
            $service->uuidService = uniqid();
            $service->Entreprise = $jsonData["entreprise"] ?? NULL;
            $return = ServiceEntity::add($sqlF, $service);
            if ($return) {
                return $service->uuidService;
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite dans la création"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function getServiceByEntreprise($siret)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(['admin', 'manager', 'agent']);
            //Récupérer les données en POST
            $entreprise = EntrepriseEntity::getEntrepriseBy($sqlF, "siretEntreprise", $siret);
            if ($entreprise === false) {
                echo json_encode(["result" => "error", "message" => "Entreprise $siret n'existe pas"]);
                exit();
            }
            //      var_dump($service);
            $return = $entreprise->getServices($sqlF);

            if ($return) {
                if (!is_array($return)) {
                    $return = [$return];
                }
                return array_reduce($return, function ($c, $e) {
                    $s = $e->getArray();
                    // unset($s["idService"]);
                    unset($s["Entreprise"]);
                    $c[] = $s;
                    return $c;
                }, []);
            } else {
                echo json_encode(["result" => "error", "message" => "Une erreur s'est produite lors de l'exécution de la requête"]);
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
    public static function getService($id = null)
    {
        try {
            $sqlF = self::getSQLFactory();
            self::checkToken(["admin", "manager", "agent", "dev"]);
            if ($id == null) {
                $return = ServiceEntity::getAll($sqlF);
                if (!$return) {
                    echo json_encode(["result" => "error", "message" => "no services found"]);
                    exit();
                }
                if (is_array($return))
                    return array_reduce($return, function ($car, $el) {
                        $arr = $el->getArray();
                        //  unset($arr["idService"]);
                        $car[] = $arr;
                        return $car;
                    }, []);
                else {
                    $arr = $return->getArray();
                    unset($arr["idService"]);
                    return $arr;
                }
            } else {
                //vérifie s'il s'agit de l'uuid ou de l'id
                if (is_numeric($id) == false) {
                    $return = ServiceEntity::getServiceBy($sqlF, "uuidService", $id);
                } else {
                    $return = ServiceEntity::getServiceBy($sqlF, "idService", $id);
                }
                if (!$return) {
                    echo json_encode(["result" => "error", "message" => "no service found"]);
                    exit();
                }
                $arr = $return->getArray();
                unset($arr["idService"]);
                return $arr;
            }
        } catch (Exception $e) {
            echo json_encode(["result" => "error", "message" => $e->getMessage()]);
            exit();
        }
    }
}