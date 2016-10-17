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

		try { 
			$db = $this->connect();
		} catch(PDOException $e) {
			$error = $e->getMessage();
		}

      $username = $db->quote($user);
      $password = $db->quote($pass);

		$result = $db->prepare("

								SELECT id_pk, user_active 
								FROM user 
								WHERE user_active = 'on' 
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
							
								SELECT first_name, permissions_fk 
								FROM user 
								WHERE id_pk = '$id'

							");

			$name = $query->fetch(PDO::FETCH_ASSOC);

			$_SESSION['user'] = $username;
			$_SESSION['first_name'] = $name['first_name'];
			$_SESSION['role'] = $name['permissions_fk'];
			header('location: index.php');
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
		ON user.permissions_fk=permissions.id_pk;

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


/*		insert(table, array) Requires table and an associative 
		array using column name as the key.

		Example:

		array(
			'first_name' => NULL,
			'last_name' => NULL,
			'username' => NULL,
			'password' => NULL,
			'user_permissions_fk' => NULL
			);

*/

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


