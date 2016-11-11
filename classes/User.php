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

		// Attempt to connect to database.
		try {

			$db = new DB();
			$db = $db->connect();

		} catch (EXCEPTION $e) {

			echo "Unable to connect to database.";
		}

		if (is_null($init)) {
			$this->result = False;
		}

		// Populate $results with associative array of
		// passed in integer (user ID)
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

			$results = $query->fetch(PDO::FETCH_ASSOC);

			// If query finds the user ID
			if ($results['ID']) {
				$this->result = True;
			} else {
				$this->result = False;
			}
		}

		if (is_string($init)) {
			$sql = 'SELECT id_pk as ID,
			first_name as fName,
			last_name as lName,
			username as uName,
			password as uPass,
			permissions_fk as uPerm,
			user_active as uActive
			FROM user
			WHERE username = "' . $init . '"';

			$query = $db->prepare($sql);
			$query->execute();

			$results = $query->fetch(PDO::FETCH_ASSOC);

			if ($results['ID']) {
				$this->result = True;
			} else {
				$this->result = False;
			}

		}

		if ($this->result) {
			$this->ID = $results['ID'];
			$this->fName = $results['fName'];
			$this->lName = $results['lName'];
			$this->uName = $results['uName'];
			$this->uPass = $results['uPass'];
			$this->uPerm = $results['uPerm'];
			$this->uActive = $results['uActive'];
		}
	}

	public function WriteUser() {

		$db = new DB;
		$db = $db->connect();

		if (isset($this->fName, $this->lName, $this->uName, $this->uPass, $this->uPerm, $this->uActive)) {
			$query = $db->prepare("INSERT INTO user (first_name, last_name, username, password, permissions_fk, user_active) VALUES (:fname, :lname, :uname, :pass, :perm, :active)");
			$query->bindValue(':fname',$this->fName);
			$query->bindValue(':lname',$this->lName);
			$query->bindValue(':uname',$this->uName);
			$query->bindValue(':pass',$this->uPass);
			$query->bindValue(':perm',$this->uPerm);
			$query->bindValue(':active',$this->uActive);
			$query->execute();
		}
	}

	public function UpdateUser() {

		$db = new DB;
		$db = $db->connect();

		if(isset($this->ID)) {
			$query = $db->prepare("UPDATE user SET first_name = :fname, last_name = :lname, username = :uname, password = :pass, permissions_fk = :perm, user_active = :active WHERE id_pk = :id");
			$query->bindValue(':fname',$this->fName);
			$query->bindValue(':lname',$this->lName);
			$query->bindValue(':uname',$this->uName);
			$query->bindValue(':pass',$this->uPass);
			$query->bindValue(':perm',$this->uPerm);
			$query->bindValue(':active',$this->uActive);
			$query->bindValue(':id',$this->ID);
			$query->execute();
		}
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

	public function GetResult(){
		return $this->result;
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