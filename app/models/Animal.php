<?php

class Animal extends Model
{
	protected $table = "animal";

	public function findByName($name)
	{
		return $this->query("SELECT * FROM @table WHERE `name` = :name", [
			":name" => $name,
		]);
	}

	public function pets($name)
	{
		return $this->query("SELECT * FROM `pet`
			LEFT JOIN @table ON `pet`.`animal_id` = @table.`id`
			WHERE @table.`name` = :name", [
				":name" => $name,
			]);
	}
}