<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
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

$hero->id = $data->id;
$hero->real_name = $data->real_name;
$hero->hero_name = $data->hero_name;
$hero->publisher = $data->publisher;
$hero->fad = $data->fad;
$hero->abilities = $data->abilities;
$hero->teams = $data->teams;


if($hero->update()){
    $hero->resetPowers();
    $hero->resetAffiliations();
    echo json_encode( array('message' => 'Hero Updated'));
}
else{
    echo json_encode( array('message' => 'Hero not Updated'));
}


?>