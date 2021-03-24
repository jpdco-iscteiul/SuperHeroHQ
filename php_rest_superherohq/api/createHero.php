<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type,
Access-Control-Allow-Methods, Authorization, X-Requested-With');


include_once '../config/Database.php';
include_once '../models/Hero.php';

//Instantiate DB and connect

$database = new Database();
$db = $database->connect();

//Instantiate hero object
$hero = new Hero($db);

$data = json_decode(file_get_contents("php://input"));

$hero->real_name = $data->real_name;
$hero->hero_name = $data->hero_name;
$hero->publisher = $data->publisher;
$hero->fad = $data->fad;
$hero->abilities = $data->abilities;
$hero->teams = $data->teams;


if($hero->create()){
    $hero->givePowers();
    $hero->giveAffiliations();
    echo json_encode( array('message' => 'Hero Created'));
}
else{
    echo json_encode( array('message' => 'Hero not Created'));
}


?>