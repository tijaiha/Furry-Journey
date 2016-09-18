<?php

Class DB {

	private function connect(){

		$dsn = 'mysql:host=127.0.0.1;dbname=reporting';
		$username = 'reportingdb';
		$password = '?4o=WxInph-w#n6%=_';
		
		try {

			$db = new PDO($dsn, $username, $password);
			$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			return $db;
			
		} catch(PDOException $e) {

			die($e->getMessage());

		};

	}

	public function runQuery($sql) {
		
		$query = $this->connect();
		$result = $query->query($sql);
		return $result;
		var_dump($result);

	}

	public function putUser($table, $data) {

		$user = $data['username'];
		if (!$this->userExists($user)) {

			echo "no user";

		} else {

			echo "user found";

		}

	}

	public function userExists($user){

		try {
			$query = $this->connect();
			$result = $query->prepare("SELECT username FROM user WHERE username LIKE ?");
			$result->bindParam(1, $user);
			$result->execute();
			$return = $result->fetchAll(PDO::FETCH_ASSOC);
			return $return[0]['username'];
			
		} catch(Exception $e) {
	
			echo $e->getMessage();
		}
	}

	public function insert($table, $data) {

		$query = $this->connect();
		$keys = array_keys($data);
		$sql = "INSERT INTO " . $table . " (";
		$counter = count($keys);
		$i = 1;

		foreach ($keys as $value) {
			if($i < $counter) {
				$sql .= $value . ", ";
				$i++;
			} else {
				$sql .= $value;
			}
		}

		$sql .= ") VALUES (";
		$i = 1;

		foreach ($data as $value) {
			if($i < $counter) {
				$sql .= "?, ";
				$i++;
			} else {
				$sql .= "?";
			}
		}

		$i = 1;
		$sql .= ")";
		echo $sql;
		$result = $query->prepare($sql);

		foreach ($data as $value) {
			$result->bindValue($i, $value);
			$i++;
		}

		$result->execute();

	}
}


