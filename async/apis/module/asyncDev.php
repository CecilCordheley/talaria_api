<?php
 namespace apis\module\asyncModule;

use DateTime;
use Exception;
use SQLEntities\EntrepriseEntity;
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
 * Ensemble des fonctions de gestion de dev asynchrone
 */
class AsyncDev{
      private static function getSQLFactory():SQLFactory{
        return new SQLFactory(null,"../include/config.ini");
    }
    private static function checkToken($roles){
        $userData = MiddleAgentV2::checkRole($roles);
             $user_required=UserEntity::getUserBy(self::getSQLFactory(),"uuidUser",$userData["user_id"]);
             if($user_required==false){
                echo json_encode(["result"=>"error","message"=>"no user finded with current token"]);
                 exit();
            }
    
    }
    public static function getStat(){
        try{
            self::checkToken(["dev","admin"]);
            $sqlF=self::getSQLFactory();
            $tickets=TicketEntity::getAll($sqlF);
            $return=array_reduce($tickets,function($car,$el) use ($sqlF){
                if(!isset($car["TYPE"])){
                    $car["TYPE"]=[];
                }
                if(!isset($car["PRIORITY"]))
                    $car["PRIORITY"]=[];
                if(!isset($car["PRIORITY"][$el->prioriteTicket]))
                    $car["PRIORITY"][$el->prioriteTicket]=1;
                else
                    $car["PRIORITY"][$el->prioriteTicket]+=1;
                $type=TypeticketEntity::getTypeticketBy($sqlF,"idTypeTicket",$el->typeTicket);

                if(!isset($car["TOTAL"]))
                    $car["TOTAL"]=0;
                if(!isset($car["TYPE"][$type->libTypeTicket]))
                    $car["TYPE"][$type->libTypeTicket]=1;
                else
                    $car["TYPE"][$type->libTypeTicket]+=1;
                $car["TOTAL"]+=1;
            return $car;
            },[]);
            return ["TICKET"=>$return];
        }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
        }
    }
    public static function cleanLog($file){
         try{
            self::checkToken(["dev"]);
            $path="../include/".$file;
            if(!file_exists($path)){
                echo json_encode(["result"=>"error","message"=>"Le fichier n'existe pas"]);
            exit();
            }
            if($file=="connexion.log")
                if(file_put_contents($path,"[]")){
                    return "$file clean";
                }else{
                    return "$file can't be clean";
                }
            elseif($file=="tokens/tokens.json"){
                if(file_put_contents($path,"{}")){
                    return "$file clean";
                }else{
                    return "$file can't be clean";
                }
            }
         }catch(Exception $e){
             echo json_encode(["result"=>"error","message"=>$e->getMessage()]);
            exit();
         }
    }
}