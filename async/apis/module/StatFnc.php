<?php
 namespace apis\module\asyncModule;

use SQLEntities\PannesEntity;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use vendor\easyFrameWork\Core\Main;
use vendor\easyFrameWork\Core\Master\SessionManager;

use DateTime;
use Exception;
use StreamBucket;

abstract class StatFnc{
    public static function getNbPannes(){
         $sqlF=new SQLFactory(null,"../include/config.ini");
        $session_manager=new SessionManager;
     //   var_dump($session_manager->get("user"));
        $curentUser=Main::fixObject($session_manager->get("user"),"SQLEntities\UsersEntity");
        return PannesEntity::getNbPannes($sqlF,$curentUser->client);
    }
    public static function getPannesByCat(){
         $sqlF=new SQLFactory(null,"../include/config.ini");
        $session_manager=new SessionManager;
     //   var_dump($session_manager->get("user"));
        $curentUser=Main::fixObject($session_manager->get("user"),"SQLEntities\UsersEntity");
        $client=$curentUser->client;
        $sql="SELECT count(panne) as nbPanne,idcategorie, LibCategorie
FROM users_found_pannes
INNER JOIN pannes p ON panne=p.id
RIGHT JOIN categorie c on p.categorie=c.idcategorie
WHERE c.client_id=$client
GROUP BY p.categorie";
      $result=  $sqlF->execQuery($sql);
      return $result;
    }
  
  
}