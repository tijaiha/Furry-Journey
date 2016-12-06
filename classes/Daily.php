<?php

/**
*
*/

class Daily
{
	private $_db,
	$_store,
	$_user,
	$_sources = array(),
	$_revenue,
	$_deduction,
	$_modified,
	$_submitted;

	public function SetStore($id){
		$this->_store = $id;
	}

	public function GetStore(){
		return $this->_store;
	}

	public function SetUser($id){
		$this->_user = $id;
	}

	public function GetUser(){
		return $this->_user;
	}

	public function SetSources($array){
		$this->_sources = $array;
	}

	public function GetSources(){
		return $this->_sources;
	}

	public function SetRevenue($value){
		$this->_revenue = $value;
	}

	public function GetRevenue(){
		return $this->_revenue;
	}

	public function SetDeduction($value){
		$this->_deduction = $value;
	}

	public function GetDeduction(){
		return $this->_deduction;
	}

	function __construct($store, $user){
		$this->_user = $user;
		$this->_store = $store;
	}

	static function FetchSources($store){
		$sql = 'SELECT
		id_pk as id,
		source_id_fk as source
		FROM store_source
		WHERE store_id_fk = :store
		AND store_source_active = 1';

		$db = new DB();
		$db->connect();
		$query = $db->prepare($sql);
		$query->bindValue(':store', $store);
		$query->execute();
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function WriteTrans(){
		foreach ($_sources as $key => $value) {
			$sql = 'INSERT INTO daily (user_id_fk, source_id_fk, store_id_fk, revenue_value, deduction_value) VALUES (:user, :source, :source, :revenue, :deduction)';
			$query = $_db->prepare($sql);
			$query->bindValue(':user', $this->_user);
			$query->bindValue(':store', $this->_store);
			$query->bindValue(':source', $value['source']);
			$query->bindValue(':revenue', $this->_revenue);
			$query->bindValue(':deduction', $this->_deduction);
		}

	}

	public function WriteHTML(){
		$sources = self::FetchSources($this->_store);
		var_dump($sources);

	}



}