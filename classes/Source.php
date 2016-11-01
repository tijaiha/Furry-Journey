<?php

Class Source {

	private $sourceID,
	$sourceName,
	$sourceActive,
	$result = null;

	function __construct($init) {
		$this->Exists($init);
	}

	// Accepts source name or source id
	// populates $this->result with associative array.

	private function Exists($init = null) {

		$db = new DB();
		$db = $db->connect();


		if (is_int($init)) {
			$sql = "SELECT id_pk as sID,
			source_name as sName,
			source_active as sActive
			FROM source
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
			source_name as sName,
			source_active as sActive
			FROM source
			WHERE source_name = "' . $init . '"';
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
			$this->sourceID = $results['sID'];
			$this->sourceName = $results['sName'];
			$this->sourceActive = $results['sActive'];
		}
	}

	public function WriteSource() {

		$db = new DB;
		$db = $db->connect();

		if($this->sourceID) {

			$query = $db->prepare("UPDATE source SET source_name = :sname, source_active = :sactive WHERE id_pk = :id");
			$query->bindValue(':sname',$this->sourceName);
			$query->bindValue(':sactive',$this->sourceActive);
			$query->bindValue(':id',$this->sourceID);

		} else {

			$query = $db->prepare("INSERT INTO source (source_name, source_active) VALUES (:sname, :sactive)");
			$query->bindValue(':sname',$this->sourceName);
			$query->bindValue(':sactive',$this->sourceActive);
		}

		$query->execute();
	}


// ----------------- Gets -----------------


	public function GetID(){
		return $this->sourceID;
	}

	public function GetName(){
		return $this->sourceName;
	}

	public function GetActive(){
		return $this->sourceActive;
	}

	public function GetResult(){
		return $this->result;
	}


// ----------------- Sets -----------------

	public function SetID($value){
		$this->sourceID = $value;
	}

	public function SetName($value){
		$this->sourceName = $value;
	}

	public function SetActive($value){
		$this->sourceActive = $value;
	}

// End of class
}