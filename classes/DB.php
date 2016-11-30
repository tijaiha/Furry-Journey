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

	public function nonActiveEmployee($id = array()) {

		$parse;

		if (isset($id)){

			$ids = implode(",", $id);
			$sql = '
			SELECT user.id_pk as id,
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

		public function AddUserStore($user, $store) {
			$db = $this ->connect();
			$sql = "SELECT id_pk as id,
			store_id_fk as sid,
			user_id_fk as uid
			FROM store_user
			WHERE (store_id_fk ='" . $store . "' AND user_id_fk ='" . $user . "')";
			$query = $db->query($sql);
			$results = $query->fetchAll(PDO::FETCH_ASSOC);
			$exists = $results['id'];

			if ($exists){
				$sql = 'UPDATE store_user SET store_id_fk = :sid, user_id_fk = :uid, store_user_active = 1 WHERE id_pk = :id';
				$query = $db->prepare($sql);
				$query->bindValue(':sid',$results['sid']);
				$query->bindValue(':uid',$results['uid']);
				$query->bindValue(':uid',$results['id']);
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
				echo $sql;
				$query = $db->prepare($sql);
				$query->bindValue(':uid',$uid);
				$query->bindValue(':sid',$sid);
				$query->execute();
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

		public function FetchSources() {

			$db = $this->connect();

			$sql = "SELECT
			id_pk as id,
			source_name as name,
			source_active as active
			FROM source
			ORDER BY source_active DESC, source_name ASC;";

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


