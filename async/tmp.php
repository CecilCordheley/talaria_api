<?php
// file: user
        
function required_get(string $key): mixed {
    if (!isset($_GET[$key])) {
        echo json_encode(["status" => "error", "message" => "Le paramètre GET '$key' est requis."]);
        exit;
    }
    return $_GET[$key];
}

function required_post(string $key,$null=true,$empty=true): mixed {
$json_data = isset($_POST)?$_POST:json_decode(file_get_contents('php://input'), true);
    if (!isset($json_data[$key])) {
        echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
        exit;
    }
        if(!$null){
        if (strtoupper($json_data[$key])=="NULL") {
                    echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
                     exit;
                }
            }
        
        if(!$empty){
        if (empty($json_data[$key])) {
                    echo json_encode(["status" => "error", "message" => "Le paramètre POST '$key' est requis."]);
                     exit;
                }
            }
        
    return $json_data[$key];
}

function fail(string $message): never {
    echo json_encode(["status" => "error", "message" => $message]);
    exit;
}
use apis\module\AsyncModule\asyncUser;
require_once "./apis/module/asyncUser.php";

function __request_getUser() {

    $id=$_GET["id"]??null;
    echo json_encode(["status" => "success", "data" => asyncUser::getUser($id)]); exit;

}
function __request_del() {

    $idUser=required_get("idUser");
    echo json_encode(["status" => "success", "data" => asyncUser::delUser($idUser)]); exit;

}
function __request_changePassWord() {

    $user=required_get("idUser");
    $newPassWord=required_get("newPassWord");
    echo json_encode(["status" => "success", "data" => asyncUser::updatePassWord($user,$newPassWord)]); exit;

}
function __request_associateService() {

    $user=required_get("idUser");
    $service=required_get("idService");
    echo json_encode(["status" => "success", "data" => asyncUser::associateService($user,$service)]); exit;

}
function __request_checkToken() {

    echo json_encode(["status" => "success", "data" => asyncUser::checkValidToken()]); exit;

}
function __request_updateUser() {

    $id=required_get("idUser");
    echo json_encode(["status" => "success", "data" => asyncUser::updateUser($id)]); exit;

}
function __request_updateData() {

    $id=required_get("idUser");
    $key=required_get("key");
    $value=required_get("value");
    echo json_encode(["status" => "success", "data" => asyncUser::updateData($id,$key,$value)]); exit;

}
function __request_createAgent() {

    $type=required_get("type");
    if(!in_array($type,['1','2','3','4','agent','manager']))
        fail("not a valid type");
    
    echo json_encode(["status" => "success", "data" => asyncUser::createUser($type)]); exit;

}
function __request_connexion() {

    $mail=required_post("mail");
    $mdp=required_post("secret");
   $result= asyncUser::connexion($mail,$mdp);
   echo json_encode(["status" => "success", "data" => $result]); exit;

}global $_MAIN;
        $_MAIN = [
    'action' => 'updateUser'
];
if (!isset($_MAIN['action'])) {
    echo json_encode(["status" => "error", "message" => "Paramètre 'action' manquant."]);
    exit;
}
$action = $_MAIN['action'];
$handler = "__request_$action";
if (!function_exists($handler)) {
    echo json_encode(["status" => "error", "message" => "Action '$action' non trouvée."]);
    exit;
}
header("Content-Type: application/json");
$handler();