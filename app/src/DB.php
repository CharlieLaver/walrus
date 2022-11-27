<?php

class DB
{
	private $conn;
	
	public function __construct()
	{
		$config = $GLOBALS['config']->get('DB');
		try {
			$this->conn = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}", $config['username'], $config['password']);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			die("Connection failed: " . $e->getMessage());
		}
	}
	
	/* Query Method Use:
		- 1st param is the SQL string.
		- 2nd param is an array of variables to replace placeholders in the sql string.
			- to repalce a table or column name add a @ on the front of the key (e.g. #table).
			- To replace pdo varible add a : on the front of the string (e.g. :username).
			- E.g. ['#table' => 'users', ':username' => 'birdperson01']
	*/
	
	public function query(string $sql, array $vars = array())
	{
		try {
			foreach($vars as $k => $v) {
				if(substr($k, 0, 1) == "@") {
					$sql = str_replace($k, $v, $sql);
					unset($vars[$k]);
				}
			}
			$stmt = $this->conn->prepare($sql);
			if(sizeof($vars)) {
				foreach($vars as $k => &$v) {
					if(substr($k, 0, 1) == ":") {
						$stmt->bindParam($k, $v);
					}
				}
			}
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			die("DB Error: " . $e->getMessage());
		}
	}
}