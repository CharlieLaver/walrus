<?php
	
class Router extends Response
{
	private $routes = [];
	
	public function __construct(array $routes)
	{
		foreach($routes as $route => $callback) {
			if(!isset($callback[0])) {
				die("No class is defined for route {$route}!");
			}
			if(!isset($callback[1])) {
				die("No method is defined for route {$route}!");
			}
			if(!class_exists($callback[0])) {
				die("Class {$callback[0]} is not defined!");
			}
			if(!method_exists($callback[0], $callback[1])) {
				die("Method {$callback[0]}::{$callback[1]} is not defined!");
			}
			$this->routes[$this->cleanUrl($route)] = $callback;
		}
		$this->map();
	}
	
	private function map()
	{
		$cleanedUrl = $this->cleanUrl(strtok($_SERVER['REQUEST_URI'], '?'));
		
		if(isset($this->routes[$cleanedUrl])) {
			
			$class = $this->routes[$cleanedUrl][0];
			$method = $this->routes[$cleanedUrl][1];
			$reflection = new ReflectionMethod($class, $method);
			
			$errors = [];
			$sortedParams = [];
			
			foreach($reflection->getParameters() as $expectedParam) {
				if(isset($_REQUEST[$expectedParam->name])) {
					$sortedParams[$expectedParam->name] = $_REQUEST[$expectedParam->name];
				} else {
					$errors[] = "EXPECTED PARAM {$expectedParam->name} NOT FOUND!";
				}
			}
			
			if(!sizeof($errors)) {
				call_user_func_array([new $class(), $method], $sortedParams);
			} else {
				$this->return($errors, false);
			}
			
			die();
			
		} else {
			die("ROUTE NOT MAPPED!");
		}
	}
	
	private function cleanUrl($url)
	{
		return preg_replace('/[^A-Za-z0-9\/-]/', '', $url);
	}
}