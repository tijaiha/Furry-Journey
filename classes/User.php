<?php

Class User {

	private $ID,
	$fName,
	$lName,
	$uName,
	$uPass,
	$uPerm,
	$uActive,
	$result = null;

	function __construct($init) {
		$this->Exists($init);
	}

	// Accepts username or user id
	// populates $this->result with associative array.
	private function Exists($init = null) {

		try {

			$db = new DB();
			$db = $db->connect();

		} catch (EXCEPTION $e) {

			echo "Unable to connect to database.";
		}


		if (is_int($init)) {
			$sql = "SELECT id_pk as ID,
					first_name as fName,
					last_name as lName,
					username as uName,
					password as uPass,
					permissions_fk as uPerm,
					user_active as uActive
					FROM user
					WHERE id_pk = " . $init;
			$query = $db->prepare($sql);
			$query->execute();

			$this->result = $query->fetch(PDO::FETCH_ASSOC); 
		} 

		if (is_string($init)) {
			$query = $db->prepare("
				SELECT id_pk as ID,
				first_name as fName,
				last_name as lName,
				username as uName,
				password as uPass,
				permissions_fk as uPerm,
				user_active as uActive
				FROM user
				WHERE username = ?");
			$query->bindValue(1, $init);
			$query->execute();


			$results = $query->fetch(PDO::FETCH_ASSOC); 

			if ($results['ID']) {
				$this->result = $results;
			} else {
				$this->result = null;
			}
		}

		if (is_null($init)) {
			$this->result = null;
			return False;
			exit();
		} 

		if ($this->result) {
			$this->ID = $result['ID'];
			$this->fName = $result['fName'];
			$this->lName = $result['lName'];
			$this->uName = $result['uName'];
			$this->uPass = $result['uPass'];
			$this->uPerm = $result['uPerm'];
			$this->uActive = $result['uActive'];
		}
	}

	public function WriteUser() {

		try {
			$db = new DB;
			$db = $db->connect();
		} catch (EXCEPTION $e) {
			echo "Unable to connect to the database.";
		}

		if($this->result) {
			$sql = "UPDATE user

					SET 
					first_name = " . $this->fName . ",
					last_name = " . $this->lName . ",
					username = " . $this->uName . ",
					password = " . $this->uPass . ",
					permissions_fk = " . $this->uPerm . ",
					user_active = " . $this->uActive . "

					WHERE
					id_pk = " . $this->ID . "
					";

		} else {
			$sql = "INSERT INTO user (
					first_name,
					last_name,
					username,
					password,
					permissions_fk,
					user_active)

					VALUES (
					$this->fName, 
					$this->lName, 
					$this->uName, 
					$this->uPass, 
					$this->uPerm, 
					$this->uActive)";
		}
		$query = $db->prepare($sql);
		$query->execute();
	}

// ----------------- Gets -----------------


	public function GetID(){
		return $this->ID;
	}

	public function GetFirst(){
		return $this->fName;
	}

	public function GetLast(){
		return $this->lName;
	}

	public function GetUser(){
		return $this->uName;
	}

	public function GetPass(){
		return $this->uPass;
	}

	public function GetPerm(){
		return $this->uPerm;
	}

	public function GetActive(){
		return $this->uActive;
	}


// ----------------- Sets -----------------

	public function SetID($value){
		$this->ID = $value;
	}

	public function SetFirst($value){
		$this->fName = $value;
	}

	public function SetLast($value){
		$this->lName = $value;
	}

	public function SetUser($value){
		$this->uName = $value;
	}

	public function SetPass($value){
		$this->uPass = $value;
	}

	public function SetPerm($value){
		$this->uPerm = $value;
	}

	public function SetActive($value){
		$this->uActive = $value;
	}

}