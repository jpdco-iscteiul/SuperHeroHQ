<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/Database.php';
include_once '../models/Hero.php';

//Instantiate DB and connect

$database = new Database();
$db = $database->connect();

//Instantiate hero object
$hero = new Hero($db);

//Post Array
$heroes_arr = array();
$heroes_arr['data'] = array();
$hero->id = $_GET['id'];
$hero->readSingle();
$hero->getAbilities();
$hero->getAffiliations();

$hero_item = array(
	'id' => $hero->id,
	'real_name' => $hero->real_name,
	'hero_name' => $hero->hero_name,
	'publisher' => $hero->publisher,
	'fad' => $hero->fad,
	'abilities' => $hero->abilities,
	'affiliations' => $hero->teams,
);

array_push($heroes_arr['data'], $hero_item);


echo json_encode($heroes_arr);


?>