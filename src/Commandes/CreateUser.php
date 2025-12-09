<?php
namespace Commandes;
require __DIR__.'/../../vendor/easyFrameWork/Core/Master/EasyFrameWork.php';

use DateTime;
use Exception;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EnvParser;

class CreateUser{
    private $nom;
    private $prenom;
    private $mail;
    private $service;
    private $type;
    public function __construct(string $nom,string $prenom,string $mail,int $service,int $type){
        $this->nom=$nom;
        $this->prenom=$prenom;
        $this->mail=$mail;
        $this->service=$service;
        $this->type=$type;
    }
    public function handle(){
        $user=new UserEntity();
        $user->nomUser=$this->nom;
        $user->prenomUser=$this->prenom;
        $user->mailUser=$this->mail;
         $arrive = date("Y-m-d");
        $date = new DateTime($arrive);
        // Ajout de 1 mois
        $date->modify('+1 months');
        //durée de validité du mot de passe
        $valid = $date->format('Y-m-d');
        $user->validiteMdp=$valid;
        $user->service_idService=$this->service;
        $user->typeUser=$this->type;
    }
}