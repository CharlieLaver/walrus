<?php

require 'vendor/autoload.php';

$config = new Config();
$db = new DB();

new Router([
	'/animal/save' => [AnimalController::class, 'save'],
	'/animal/get' => [AnimalController::class, 'get'],
	'/pet/save' => [PetController::class, 'save'],
	'/pet/get' => [PetController::class, 'get'],
	'/animal/pets' => [AnimalController::class, 'pets'],
]);