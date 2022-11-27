<?php

class PetController extends Response
{
	public function get($id)
	{
		$pet = new Pet($id);
		return $this->return($pet->columns);
	}
	
	public function save($name, $species)
	{
		$animal = new Animal();
		$animalID = current($animal->findByName($species))['id'];
		$pet = new Pet();
		$pet->animal_id($animalID)->name($name)->save();
		return $this->return("Pet saved successfully!");
	}
}