<?php

class Model
{
	private $db;
	private $id = false;
	public $columns = [];
	
	public function __construct($id = false)
	{
		if(!isset($GLOBALS['db'])) {
			die("You must create an instance of DB before setting up a Model!");
		}
		
		$this->db = $GLOBALS['db'];
		$schema = $this->query("SHOW COLUMNS FROM @table");
		
		if($id) {
			$this->id = $id;
			$row = current($this->query("SELECT * FROM @table WHERE `id` = :id", [
				':id' => $this->id,
			]));
		}
		
		foreach($schema as $col) {
			$this->columns[$col['Field']] = ($id && isset($row[$col['Field']]) ? $row[$col['Field']] : false);
		}
	}
	
	public function __call(string $method, array $args)
	{
		if(isset($this->columns[$method]) && isset($args[0])) {
			$this->columns[$method] = $args[0];
			return $this;
		}
	}
	
	public function save()
	{
		$pdoVars = [];
		$updateStr = "";
		$i = 0;
		foreach($this->columns as $k => $v) {
			$i++;
			$pdoVars[":{$k}"] = $v;
			$updateStr .= "{$k} = :{$k}";
			if($i != sizeof($this->columns)) {
				$updateStr .= ", ";
			}
		}
		
		if(isset($this->columns['id']) && $this->columns['id']) {
			$sql = "UPDATE @table SET {$updateStr} WHERE `id` = {$this->columns['id']}";
		} else {
			unset($this->columns['id']);
			unset($pdoVars[':id']);
			$sql = "INSERT INTO @table (" . join(',', array_keys($this->columns)) . ") VALUES (" . join(',', array_keys($pdoVars)) . ")";
		}
		
		return $this->query($sql, $pdoVars);
	}
	
	public function query($sql, $vars = array())
	{
		if($this->id) {
			$vars[":id"] = $this->id;
		}
		$vars["@table"] = $this->table;
		return $this->db->query($sql, $vars);
	}
}