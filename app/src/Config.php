<?php

class Config
{
	public $ini = false;
	
	public function __construct()
	{
		$this->ini = parse_ini_file('config.ini', true);
	}
	
	public function get($section)
	{
		if(isset($this->ini[$section])) {
			return $this->ini[$section];
		} else {
			die("Section {$section} not defined in config.ini!");
		}
	}
}