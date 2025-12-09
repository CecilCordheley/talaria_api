<?php
namespace Commandes;
require __DIR__.'/../../vendor/easyFrameWork/Core/Master/EasyFrameWork.php';

use DateTime;
use Exception;
use SQLEntities\ServiceEntity;
use vendor\easyFrameWork\Core\Master\Cryptographer;
use vendor\easyFrameWork\Core\Master\EasyFrameWork;
use vendor\easyFrameWork\Core\Master\EnvParser;

class createService{
    private $nom;
    private $entreprise;
    private $desc;
    public function __construct(string $nom,string $desc,int $entreprise){
        $this->nom=$nom;
        $this->desc=$desc;
        $this->entreprise=$entreprise;
    }
    public function handle(){
        $service=new ServiceEntity();
        $service->nomService=$this->nom;
        $service->descService=$this->desc;
        $service->Entreprise=$this->entreprise;
        $service->isActiv=true;
    }
}