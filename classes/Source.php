<?php
Class Source {
	private $ID,
	$sName,
	$sActive,
	$result = null;
	function __construct($init) {
		$this->Exists($init);
	}
	// Accepts source name or source id
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
		// passed in integer (source ID)
		if (is_int($init)) {
			$sql = "SELECT id_pk as ID,
			source_name as sName,
			source_active as sActive
			FROM source
			WHERE id_pk = " . $init;
			$query = $db->prepare($sql);
			$query->execute();
			$results = $query->fetch(PDO::FETCH_ASSOC);
			// If query finds the source ID
			if ($results['ID']) {
				$this->result = True;
			} else {
				$this->result = False;
			}
		}
		if (is_string($init)) {
			$sql = 'SELECT id_pk as ID,
			source_name as sName,
			source_active as sActive
			FROM source
			WHERE source_name LIKE "' . $init . '"';
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
	public function Writesource() {
		$db = new DB;
		$db = $db->connect();
		if (isset($this->sName, $this->sActive)) {
			$query = $db->prepare("INSERT INTO source (source_name, source_active) VALUES (:sname, :sactive)");
			$query->bindValue(':sname',$this->sName);
			$query->bindValue(':sactive',$this->sActive);
			$query->execute();
		}
	}
	public function Updatesource() {
		$db = new DB;
		$db = $db->connect();
		if(isset($this->ID)) {
			$query = $db->prepare("UPDATE source SET source_name = :sname, source_active = :sactive WHERE id_pk = :id");
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
		$this->sName = $value;
	}
	public function SetActive($value){
		$this->sActive = $value;
	}
}