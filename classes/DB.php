<?php

Class DB {

	public function connect(){

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

	public function login($user, $pass) {

		$db = $this->connect();

		$username = $db->quote($user);
		$password = $db->quote($pass);

		$result = $db->prepare("

			SELECT id_pk, user_active
			FROM user
			WHERE user_active = 1
			AND username = lower(?)
			AND password = ?

			");

		$result->bindParam(1, $user);
		$result->bindParam(2, $pass);
		$result->execute();

		$row = $result->fetch(PDO::FETCH_ASSOC);

		$id = $row['id_pk'];

		if($row) {
			$_SESSION['error'] = "";
			$query = $db->query("

				SELECT first_name, last_name, permissions_fk, id_pk
				FROM user
				WHERE id_pk = '$id'

				");

			$name = $query->fetch(PDO::FETCH_ASSOC);

			$_SESSION['user'] = $username;
			$_SESSION['first_name'] = $name['first_name'];
			$_SESSION['last_name'] = $name['last_name'];
			$_SESSION['role'] = $name['permissions_fk'];
			$_SESSION['user_id'] = $name['id_pk'];
			return true;
		}else {
			return false;
		}
	}

	public function fetchUsers() {
		$query = $this->connect();
		$result = $query->query("

			SELECT 	user.id_pk as id,
			user.first_name as first,
			user.last_name as last,
			user.username as user,
			user.user_active as active,
			permissions.permission_name as role
			FROM user
			LEFT JOIN permissions
			ON user.permissions_fk=permissions.id_pk
			ORDER BY user.user_active DESC, user.permissions_fk ASC, user.first_name ASC;

			");
		$return = $result->fetchAll(PDO::FETCH_ASSOC);
		return $return;
	}

	public function userExists($user){

		try {
			$query = $this->connect();
			$result = $query->prepare("

				SELECT username
				FROM user
				WHERE username
				LIKE ?

				");


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


	public function FetchStores() {

		$db = $this->connect();

		$sql = "SELECT
		id_pk as id,
		store_name as name,
		store_active as active
		FROM store
		ORDER BY store_active DESC, store_name ASC;";

		$query = $db->query($sql);
		$results = $query->fetchAll(PDO::FETCH_ASSOC);

		return $results;

	}

	public function FetchSources() {

		$db = $this->connect();

		$sql = "SELECT
		id_pk as id,
		source_name as name,
		source_active as active
		FROM source";

		$query = $db->query($sql);
		$results = $query->fetchAll(PDO::FETCH_ASSOC);

		return $results;

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


