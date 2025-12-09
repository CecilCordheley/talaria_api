<?php
 namespace apis\module\asyncModule;

use vendor\easyFrameWork\Core\Master\SQLFactory;


abstract class Async{
    protected static function getSQLFactory(){
       
        return new SQLFactory(null,"../include/config.ini");
    }
    
}