#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/easyFrameWork/Core/Master/Autoloader.php';

use Commandes\GeneratePage;

use SQLEntities\EntrepriseEntity;
use SQLEntities\Service;
use SQLEntities\UserEntity;
use vendor\easyFrameWork\Core\Master\CommandLiner;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EnvParser;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\Autoloader;
use vendor\easyFrameWork\Core\Master\SqlEntities;
use vendor\easyFrameWork\Core\Master\SQLFactory;
require __DIR__ . '/../src/Commandes/CrypterCommande.php';
require __DIR__ . '/../src/Commandes/GenerateEntities.php';
require __DIR__."/../src/Commandes/GeneratePage.php";
use Commandes\CrypterCommande;
use Commandes\GenerateEntities;
use SQLEntities\ServiceEntity;
use SQLEntities\Users;

EasyFrameWork::INIT("./../vendor/easyFrameWork/Core/config/config.json");
Autoloader::register();
$commande = $argv[1] ?? null;
$argument = $argv[2] ?? null;
// Vérification de la commande et exécution
$commands=[
    ["name"=>"generatePage","desc"=>"Generate page with controller"],
    ["name"=>"generateEntities","desc"=>"Generate SQLEntities from table","param"=>["tableName"]],
    ["name"=>"HashCrypt","desc"=>"Encrypt and Hash a string with the MD2 algorithm","param"=>["textToEncrypt"]],
    ["name"=>"crypt","desc"=>"Encrypt a string","param"=>["textToEncrypt"]],
    ["name"=>"createEntreprise","desc"=>"permet de créer une entreprise"],
    ["name"=>"createService","desc"=>"permet de créer un service"],
    ["name"=>"decrypt","desc"=>"Decrypt a string","param"=>["textToDecrypt"]],
    [
        "name"=>"generatePassWord",
        "desc"=>"Create a random password by the specific method for user Instance",
        "param"=>["size"]
    ],
    ["name"=>"createUser","desc"=>"create an user as Admin for agent_tbl"],
];
switch ($commande) {
    case "-h":{
        foreach($commands as $c){
            echo $c["name"]."\t=>\t".$c["desc"]."\n------------------------------\n";
        }
        break;
    }
    case "createService":{
        echo "Vous allez créer un service qui ne sera pas rattaché à une entreprise\n";
         $SqlF=new SQLFactory(null,"../include/config.ini");
         $end="O";
         while($end=="O"){
        $nom=CommandLiner::readLine("Nom du service : ","string");
        $desc=CommandLiner::readLine("descriptif : ","string");
        $service=new ServiceEntity;
        $service->nomService=$nom;
        $service->descService=$desc;
        $service->createAt=date("Y-m-d");
        $service->isActiv=1;
        $return=ServiceEntity::add($SqlF,$service);
        if($return){
            echo "Service Créé !\n----------";
            $end=CommandLiner::readLine("\nSouhaitez-vous créer un nouveau service ? O-N");
        }else{
            echo "Une erreur s'est produite ";
            $end="N";
        }
    }
        break;
    }
    case "createEntreprise":{
        $SqlF=new SQLFactory(null,"../include/config.ini");
        $nom=CommandLiner::readLine("Nom de l'entreprise : ","string");
        $adr=CommandLiner::readLine("Adresse de l'entreprise : ","string");
        $cp=CommandLiner::readLine("CP de l'entreprise : ","string");
        $ville=CommandLiner::readLine("Ville de l'entreprise : ","string");
        $siret="";
        $alpha="0123456789";
        for($i=0;$i<14;$i++){
            $index=rand(0,strlen($alpha)-1);
            $siret.=$alpha[$index];
        }
     //   CommandLiner::writeLine(["siret"=>$siret,"nom"=>$nom,"adresse"=>$adr,"CP"=>$cp]);
        $type="client";
        $createAt=date("Y-m-d");
        $data="{}";
        $entreprise=new EntrepriseEntity();
        $entreprise->nomEntreprise=$nom;
        $entreprise->adresseEntrerprise=$adr;
        $entreprise->cpEntreprise=$cp;
        $entreprise->villeEntreprise=$ville;
        $entreprise->siretEntreprise=$siret;
        $entreprise->dataEntreprise=$data;
        $entreprise->typeEntreprise=$type;
        $entreprise->created_At=$createAt;
        $return = EntrepriseEntity::add($SqlF,$entreprise);
        if($return){
            echo "Entreprise créée";
        }else{
            echo "une erreur s'est produite";
        }
        break;
    }
    case "createAdmin":{
        $SqlF=new SQLFactory(null,"../include/config.ini");
        $nom=readline("nom de l'agent : ");
        $prenom=readline("prenom de l'agent : ");
        $mail=readline("mail de l'agent : ");
         $crypto=new Cryptographer();
        $mdp="";
        $alpha="ACDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz0123456789@^";
        for($i=0;$i<8;$i++){
            $index=rand(0,strlen($alpha));
            $mdp.=$alpha[$index];
        }
        $crypt=$crypto->hashString($mdp);
        $user=new UserEntity();
        $user->nomUser=$nom;
        $user->prenomUser=$prenom;
        $user->mailUser=$mail;
        $user->mdpUser=$crypt;
          $arrive = date("Y-m-d");
        $date = new DateTime($arrive);
        // Ajout de 1 mois
        $date->modify('+1 months');
        //durée de validité du mot de passe
        $valid = $date->format('Y-m-d');
        $user->validiteMdp=$valid;
        $user->typeUser=1;
        $user->uuidUser=uniqid();
        $user->dataAgent="{}";
        $return=UserEntity::add($SqlF,$user);
        if($return){
            echo "Admin Créé avec $mdp";
        }else{
            echo "Une erreur s'est produite";
        }
        break;
    }
    case "generatePage":{
        $pageName=readline("Page Name : ");
        $pageGenerator=new GeneratePage($pageName);
        $pageGenerator();
        break;
    }
    case "allCommands":{
        echo "all commands available\n---------\n";
        array_walk($commands,function($el){
            echo $el["name"];
            echo "\n {$el["desc"]}\n parameters:";
            foreach($el["param"] as $p){
                echo "\n\t".$p;
            }
            echo "\n-------\n";
        });
        echo "\nEnds of commands";
        break;
    }
    case "generatePassWord":{
        $crypto=new Cryptographer();
        $size=$argument;
        $mdp="";
        $alpha="ACDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz0123456789@^";
        for($i=0;$i<$size;$i++){
            $index=rand(0,strlen($alpha));
            $mdp.=$alpha[$index];
        }
        echo "clean password : $mdp";
        $crypt=$crypto->hashString($mdp);
        echo "\n------------\n";
        echo "crypt password : $crypt";
        break;
    }
    case "generateEntities": {
        $env=new EnvParser(EasyFrameWork::$Racines["dirAccess"]."/.env");
        $base=$env->get("BDD");
            $gen = new GenerateEntities();
            if ($argument != null)
                $gen->handle($argument);
            else{
                echo "Génération de toute les table de la base {$base}";
                $sqlF = new SQLFactory(null,__DIR__."/../include/config.ini");
                $tables=$sqlF->getTableSchema();
                foreach($tables as $t){
                    $gen->handle($t["TABLE_NAME"]);
                }
            }
            //  echo "test".PHP_EOL;
            break;
        }
    case 'HashCrypt':{
            $crypter = new CrypterCommande();
            echo "Talaria Says : ".$crypter($argument) . PHP_EOL;
            break;
    }
    case 'crypt':{
        $crypter = new CrypterCommande();
        echo "Talaria Says : ".$crypter($argument,1) . PHP_EOL;
        break;
    }
    case 'decrypt':{
            $crypter = new CrypterCommande();
            echo "Talaria Says : ".$crypter($argument,2) . PHP_EOL;
            break;
    }
    default:
        echo "Talaria Says : Commande inconnue" . PHP_EOL;
        break;
}
