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

//Hero query
$result = $hero->read();
//get row count
$num = $result->rowCount();

if($num > 0){
	//Post Array
	$heroes_arr = array();
	$heroes_arr['data'] = array();

	while($row = $result->fetch(PDO::FETCH_ASSOC)){
		extract($row);

		$hero->id = $id;

		$hero_item = array(
			'id' => $id,
			'hero_name' => $hero_name,
		);

		array_push($heroes_arr['data'], $hero_item);
	}

	echo json_encode($heroes_arr);
} else{
	echo json_encode(
		array('message' => 'No Heroes Found')
		);
}

?>