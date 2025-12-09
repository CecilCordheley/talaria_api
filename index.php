<?php
require_once ("./vendor/easyFrameWork/Core/Master/EasyFrameWork.php");

use SQLEntities\EtatticketEntity;
use SQLEntities\TicketEntity;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;

use vendor\easyFrameWork\Core\Master\Router;

use vendor\easyFrameWork\Core\Master\Autoloader;
use Vendor\EasyFrameWork\Core\Master\DRP;
use vendor\easyFrameWork\Core\Master\SQLFactory;


EasyFrameWork::INIT("./vendor/easyFrameWork/Core/config/config.json");
Autoloader::register();
$SqlF=new SQLFactory();
/*
//$SqlF->getPdo();
$user_pointer=new DRP("user","idUser:1",$SqlF->getPdo());
$user_pointer->values->mailUser="newMail";
echo $user_pointer->values->mailUser;*/
$t=TicketEntity::getTicketBy($SqlF,"uuidTicket","TICKT-691f12ccadd17");
$newState=EtatticketEntity::getEtatticketBy($SqlF,"refEtatTicket","VALI-D83EF");
$state=$t->changeEtat($SqlF,$newState,"test");