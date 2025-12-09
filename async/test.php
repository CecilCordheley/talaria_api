<?php
// file: entreprise
        
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
use apis\module\AsyncModule\asyncEntreprise;
require_once "./apis/module/asyncEntreprise.php";

function __request_get() {

    $id=$_GET["siret"]??NULL;
    echo json_encode(["status" => "success", "data" => asyncEntreprise::getEntreprise($id)]); exit;

}
function __request_create() {

    echo json_encode(["status" => "success", "data" => asyncEntreprise::addEntreprise()]); exit;

}
function __request_update() {

    $id=required_get("siret");
    echo json_encode(["status" => "success", "data" => asyncEntreprise::update($id)]); exit;

}global $_MAIN;
        $_MAIN = [
    'action' => 'update'
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