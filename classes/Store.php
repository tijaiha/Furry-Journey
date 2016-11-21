<?php
Class Store {
	private $ID,
	$sName,
	$sActive,
	$result = null;
	function __construct($init) {
		$this->Exists($init);
	}
	// Accepts store name or store id
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
		// passed in integer (store ID)
		if (is_int($init)) {
			$sql = "SELECT id_pk as ID,
			store_name as sName,
			store_active as sActive
			FROM store
			WHERE id_pk = " . $init;
			$query = $db->prepare($sql);
			$query->execute();
			$results = $query->fetch(PDO::FETCH_ASSOC);
			// If query finds the store ID
			if ($results['ID']) {
				$this->result = True;
			} else {
				$this->result = False;
			}
		}
		if (is_string($init)) {
			$sql = 'SELECT id_pk as ID,
			store_name as sName,
			store_active as sActive
			FROM store
			WHERE store_name LIKE "' . $init . '"';
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
			$this->sName = $results['sName'];
			$this->sActive = $results['sActive'];
		}
	}
	public function WriteStore() {
		$db = new DB;
		$db = $db->connect();
		if (isset($this->sName, $this->sActive)) {
			$query = $db->prepare("INSERT INTO store (store_name, store_active) VALUES (:sname, :sactive)");
			$query->bindValue(':sname',$this->sName);
			$query->bindValue(':sactive',$this->sActive);
			$query->execute();
		}
	}
	public function UpdateStore() {
		$db = new DB;
		$db = $db->connect();
		if(isset($this->ID)) {
			$query = $db->prepare("UPDATE store SET store_name = :sname, store_active = :sactive WHERE id_pk = :id");
			$query->bindValue(':sname',$this->sName);
			$query->bindValue(':sactive',$this->sActive);
			$query->bindValue(':id',$this->ID);
			$query->execute();
		}
	}
// ----------------- Gets -----------------
	public function GetID(){
		return $this->ID;
	}
	public function GetName(){
		return $this->sName;
	}
	public function GetActive(){
		return $this->sActive;
	}
	public function GetResult(){
		return $this->result;
	}
// ----------------- Sets -----------------
	public function SetID($value){
		$this->ID = $value;
	}
	public function SetName($value){
		$this->fName = $value;
	}
	public function SetActive($value){
		$this->sActive = $value;
	}
}