<?php

Class Store {

	private $storeID,
	$storeName,
	$storeActive,
	$result = null;

	function __construct($init) {
		$this->Exists($init);
	}

	// Accepts store name or store id
	// populates $this->result with associative array.

	private function Exists($init = null) {

		$db = new DB();
		$db = $db->connect();


		if (is_int($init)) {
			$sql = "SELECT id_pk as sID,
			store_name as sName,
			store_active as sActive
			FROM store
			WHERE id_pk = " . $init;
			$query = $db->prepare($sql);
			$query->execute();

			$results = $query->fetch(PDO::FETCH_ASSOC);
			
			if ($results['sName']) {
				$this->result = True;
			} else {
				$this->result = False;
			}
		}

		if (is_string($init)) {
			$sql = 'SELECT id_pk as sID,
			store_name as sName,
			store_active as sActive
			FROM store
			WHERE store_name = "' . $init . '"';
			$query = $db->prepare($sql);
			$query->execute();

			$results = $query->fetch(PDO::FETCH_ASSOC); 

			if ($results['sID']) {
				$this->result = True;
			} else {
				$this->result = False;
			}
		}

		if (is_null($init)) {
			$this->result = False;
			return False;
			exit();
		} 

		if ($this->result) {
			$this->storeID = $results['sID'];
			$this->storeName = $results['sName'];
			$this->storeActive = $results['sActive'];
		}
	}

	public function WriteStore() {

		$db = new DB;
		$db = $db->connect();

		if($this->result) {

			$query = $db->prepare("UPDATE store SET store_name = :sname, store_active = :sactive WHERE id_pk = :id");
			$query->bindValue(':sname',$this->storeName);
			$query->bindValue(':sactive',$this->storeActive);
			$query->bindValue(':id',$this->storeID);

		} elseif (isset($this->storeName, $this->storeActive)) {

			$query = $db->prepare("INSERT INTO store (store_name, store_active) VALUES (:sname, :sactive)");
			$query->bindValue(':sname',$this->storeName);
			$query->bindValue(':sactive',$this->storeActive);
		}

		$query->execute();
	}


// ----------------- Gets -----------------


	public function GetID(){
		return $this->storeID;
	}

	public function GetName(){
		return $this->storeName;
	}

	public function GetActive(){
		return $this->storeActive;
	}

	public function GetResult(){
		return $this->result;
	}


// ----------------- Sets -----------------

	public function SetID($value){
		$this->storeID = $value;
	}

	public function SetName($value){
		$this->storeName = $value;
	}

	public function SetActive($value){
		$this->storeActive = $value;
	}

// End of class
}