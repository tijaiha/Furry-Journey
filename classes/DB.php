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

				SELECT first_name as first, last_name as last, username as user, permissions_fk as role, id_pk as id
				FROM user
				WHERE id_pk = '$id'

				");

			$name = $query->fetch(PDO::FETCH_ASSOC);

			$_SESSION['user'] = $name['user'];
			$_SESSION['first_name'] = $name['first'];
			$_SESSION['last_name'] = $name['last'];
			$_SESSION['role'] = $name['role'];
			$_SESSION['user_id'] = $name['id'];
			return true;
		}else {
			return false;
		}
	}

	public function nonActiveEmployee($id = array()) {

		$parse;
		if (!empty($id)){

			$ids = implode(",", $id);
			$sql = 'SELECT user.id_pk as id,
			user.first_name as first,
			user.last_name as last,
			permissions.permission_name as role
			FROM user
			LEFT JOIN permissions
			ON user.permissions_fk=permissions.id_pk
			WHERE user.id_pk NOT IN (' . $ids . ') AND user.user_active = 1';

			$query = $this->connect();
			$result = $query->query($sql);
			$parse = $result->fetchAll(PDO::FETCH_ASSOC);

		} else {

			$sql = '
			SELECT user.id_pk as id,
			user.first_name as first,
			user.last_name as last,
			permissions.permission_name as role
			FROM user
			LEFT JOIN permissions
			ON user.permissions_fk=permissions.id_pk
			WHERE user.user_active = 1';

			$query = $this->connect();
			$result = $query->query($sql);
			$parse = $result->fetchAll(PDO::FETCH_ASSOC);

		}

		foreach ($parse as $key => $value) {
			echo '<option value="' . $value['id'] . '">' . $value['first'] . ' ' . $value['last'] . ' (' . $value['role'] . ')</option>';
		}
	}

	public function nonActiveSource($id = array()) {

		$parse;
		if (!empty($id)){

			$ids = implode(",", $id);

			$sql = 'SELECT id_pk as id,
			source_name as source,
			source_active as active
			FROM source
			WHERE id_pk NOT IN (' . $ids . ') AND source_active = 1';

			$query = $this->connect();
			$result = $query->query($sql);
			$parse = $result->fetchAll(PDO::FETCH_ASSOC);

		} else {

			$sql = 'SELECT id_pk as id,
			source_name as source,
			source_active as active
			FROM source
			WHERE source_active = "1"';

			$query = $this->connect();
			$result = $query->query($sql);
			$parse = $result->fetchAll(PDO::FETCH_ASSOC);
		}

		foreach ($parse as $key => $value) {
			echo '<option value="' . $value['id'] . '">' . $value['source'] . '</option>';
		}
	}

	public function fetchUsers() {
		$query = $this->connect();
		$result = $query->query("

			SELECT user.id_pk as id,
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

	public function FetchSources($id = null) {

		if ($id == null){
			$db = $this->connect();

			$sql = "SELECT
			id_pk as id,
			source_name as name,
			source_active as active
			FROM source
			ORDER BY source_active DESC, source_name ASC;";

			$query = $db->prepare($sql);
			$query->bindValue(':sid',$id);
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_ASSOC);

			return $results;
		} else {

			$db = $this->connect();

			$sql = "SELECT
			store_source.id_pk as id,
			store_source.source_id_fk as sid,
			source.source_name as source,
			store_source.store_source_active as active
			FROM store_source
			LEFT JOIN source
			ON store_source.source_id_fk=source.id_pk
			WHERE store_source.store_id_fk = :sid AND store_source.store_source_active = 1
			ORDER BY store_source.store_source_active DESC, store_source.source_id_fk ASC;";


			$query = $db->prepare($sql);
			$query->bindValue(':sid',$id);
			$query->execute();
			$results = $query->fetchAll(PDO::FETCH_ASSOC);

			return $results;
		}
	}

	public function AddUserStore($user, $store) {
		$db = $this ->connect();
		$sql = "SELECT id_pk as id,
		store_id_fk as sid,
		user_id_fk as uid
		FROM store_user
		WHERE (store_id_fk ='" . $store . "' AND user_id_fk ='" . $user . "')";
		$query = $db->query($sql);
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$exists = $results[0]['id'];

		if ($exists){
			$sql = 'UPDATE store_user SET store_id_fk = :sid, user_id_fk = :uid, store_user_active = 1 WHERE id_pk = :id';
			$query = $db->prepare($sql);
			$query->bindValue(':sid',$results[0]['sid']);
			$query->bindValue(':uid',$results[0]['uid']);
			$query->bindValue(':id',$results[0]['id']);
			$query->execute();
		} else {

			$sql = 'INSERT INTO store_user (store_id_fk, user_id_fk, store_user_active) VALUES (:sid, :uid, 1)';
			$query = $db->prepare($sql);
			$query->bindValue(':sid',$store);
			$query->bindValue(':uid',$user);
			$query->execute();
		}
	}

	public function RemoveUserStore($uid, $sid) {
		$db = $this->connect();
		$sql = 'UPDATE store_user SET store_user_active = 0 WHERE user_id_fk = :uid AND store_id_fk = :sid';
		$query = $db->prepare($sql);
		$query->bindValue(':uid',$uid);
		$query->bindValue(':sid',$sid);
		$query->execute();
	}

	public function RemoveStoreSource($stid, $soid) {
		$db = $this->connect();
		$sql = 'UPDATE store_source SET store_source_active = 0 WHERE source_id_fk = :soid AND store_id_fk = :stid';
		$query = $db->prepare($sql);
		$query->bindValue(':soid',$soid);
		$query->bindValue(':stid',$stid);
		$query->execute();
	}

	public function AddStoreSource($store, $source) {

		$db = $this ->connect();

		$sql = "SELECT id_pk as id,
		source_id_fk as soid,
		store_id_fk as stid
		FROM store_source
		WHERE (store_id_fk ='" . $store . "' AND source_id_fk ='" . $source . "')";
		$query = $db->query($sql);
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$exists = $results[0]['id'];

		if ($exists){

			$sql = 'UPDATE store_source SET store_id_fk = :stid, source_id_fk = :soid, store_source_active = 1 WHERE id_pk = :id';
			$query = $db->prepare($sql);
			$query->bindValue(':stid',$results[0]['stid']);
			$query->bindValue(':soid',$results[0]['soid']);
			$query->bindValue(':id',$results[0]['id']);
			$query->execute();

		} else {

			$sql = 'INSERT INTO store_source (store_id_fk, source_id_fk, store_source_active) VALUES (:stid, :soid, 1)';
			$query = $db->prepare($sql);
			$query->bindValue(':stid',$store);
			$query->bindValue(':soid',$source);
			$query->execute();

		}
	}

	public function FetchEmployees($id) {

		$db = $this->connect();

		$sql = "SELECT
		store_user.id_pk as id,
		user.id_pk as uid,
		user.first_name as first_name,
		user.last_name as last_name,
		permissions.permission_name as role
		FROM store_user
		LEFT JOIN store
		ON store_user.store_id_fk=store.id_pk
		LEFT JOIN user
		ON store_user.user_id_fk=user.id_pk
		LEFT JOIN permissions
		ON user.permissions_fk=permissions.id_pk
		WHERE store_user.store_id_fk = " . $id . " AND user.user_active = 1 AND store_user.store_user_active = 1";

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


