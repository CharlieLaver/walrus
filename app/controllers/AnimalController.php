<?php

class AnimalController extends Response
{
	public function get($id)
	{
		$animal = new Animal($id);
		return $this->return($animal->columns);
	}
	
	public function save($name, $family)
	{
		$animal = new Animal();
		$animal->name($name)->family($family)->save();
		return $this->return("Animal saved successfully!");
	}

	public function pets($name)
	{
		$animal = new Animal();
		return $this->return($animal->pets($name));
	}
}