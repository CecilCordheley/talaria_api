<?php
// file: ticket
        
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
use apis\module\AsyncModule\asyncTicket;
require_once "./apis/module/asyncTicket.php";

function __request_createTicket() {

    echo json_encode(["status" => "success", "data" => asyncTicket::createTicket()]); exit;

}
function __request_changeState() {

    $idTicket=required_get("idTicket");
    $newState=required_get("idState");
    $comment=$_GET["comment"]??"";
    echo json_encode(["status" => "success", "data" => asyncTicket::changeState($idTicket,$idState,$comment)]); exit;

}global $_MAIN;
        $_MAIN = [
    'action' => 'changeState'
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