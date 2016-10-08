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

		}

	}

	public function runQuery($sql) {
		
		$query = $this->connect();
		$result = $query->query($sql);
		return $result;
		var_dump($result);

	}

	public function fetchUsers() {
		$query = $this->connect();
		$result = $query->query("

			SELECT 	user.id_pk as id,
					user.first_name as first, 
					user.last_name as last, 
					user.username as user, 
					permissions.permission_name as role
			FROM user
			LEFT JOIN permissions
			ON user.permissions_fk=permissions.id_pk;

			");
		$return = $result->fetchAll(PDO::FETCH_ASSOC);
		return $return;
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

			
		} catch(Exception $e) {

			die($e->getMessage());

		}

		$result->bindParam(1, $user);
		$result->execute();
		$return = $result->fetchAll(PDO::FETCH_ASSOC);
		if ($return[0]['username']) {
			return true;
		} else {
			return false;
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
		//echo $sql;
		$result = $query->prepare($sql);

		foreach ($data as $value) {
			$result->bindValue($i, $value);
			$i++;
		}

		$result->execute();

	}
}


