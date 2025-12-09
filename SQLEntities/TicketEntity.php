<?php
namespace SQLEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
use SQLEntities\Ticket;
use vendor\easyFrameWork\Core\Main;
use Exception;
/**
* Class personnalisée pour la table `Ticket`.
* Hérite de `Ticket`. Ajoutez ici vos propres méthodes.
*/
class TicketEntity extends Ticket
{
   // Ajoutez vos méthodes ici

   /**
    * retourne les états d'un ticket
    * @param \vendor\easyFrameWork\Core\Master\SQLFactory $sqlF
    * @return void
    */public function getEtats(SQLFactory $sqlF): array {
    $changes = ChangeetatEntity::getChangeetatBy($sqlF, "Ticket", $this->idTicket);

    return is_array($changes) ? $changes : [$changes];
}
public function lastState($sqlF):mixed{
  $states = $this->getEtats($sqlF);

    // Tri par date
    usort($states, function(ChangeetatEntity $a, ChangeetatEntity $b) {
        return strtotime($a->dateEtatTicket) <=> strtotime($b->dateEtatTicket);
    });
    $return=[];
    $lastEl=end($states);
    $return["ticket_data"]=TicketEntity::getTicketBy($sqlF,"idTicket",$lastEl->Ticket);
    $return["etat_data"]=EtatticketEntity::getEtatticketBy($sqlF,"idEtatTicket",$lastEl->EtatTicket);
    // Dernier état
   return $return;
}
  public function changeEtat(SQLFactory $sqlF, EtatticketEntity $newState,$comment=""): bool
{
    // Récupération de l’historique
    $states = $this->getEtats($sqlF);

    // Tri par date
    usort($states, function(ChangeetatEntity $a, ChangeetatEntity $b) {
        return strtotime($a->dateEtatTicket) <=> strtotime($b->dateEtatTicket);
    });

    // Dernier état
    $lastState = end($states);

    // Si pas de changement → on ne fait rien
    if ($lastState->EtatTicket == $newState->idEtatTicket) {
        return true;
    }

    // Création du nouvel état
    $change = new ChangeetatEntity();
    $change->Ticket = $this->idTicket;
    $change->EtatTicket = $newState->idEtatTicket;
    $change->dateEtatTicket = date("Y-m-d H:i:s");
    $change->comment=$comment;
    // Sauvegarde
    return ChangeetatEntity::add($sqlF,$change);
 //  return true;
}
   public static function getAll($sqlF){
    $arr=Ticket::getAll($sqlF);
    if($arr){
      if(gettype($arr)=="array"){
    return array_reduce(Ticket::getAll($sqlF),function($c,$e){
      $c[]=Main::fixObject($e,"SQLEntities\TicketEntity");
      return $c;
    },[]);
  }else
    return Main::fixObject($arr,"SQLEntities\TicketEntity");
    }else
    return false;
  }
    public static function getTicketBy($sqlF,$key,$value,$filter=null){
      $arr=Ticket::getTicketBy($sqlF,$key,$value,$filter);
    if($arr){
      if(gettype($arr)=="array"){
      return array_reduce($arr,function($c,$e){
        $c[]=Main::fixObject($e,"SQLEntities\TicketEntity");
        return $c;
      },[]);
    }else return Main::fixObject($arr,"SQLEntities\TicketEntity");
    }else{
      return false;
    }
      }
 }