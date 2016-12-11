<?php

/**
*
*/

class Daily
{
	private $_db,
	$_store,
	$_user,
	$_source,
	$_revenue,
	$_revenuevalue = 0,
	$_deduction,
	$_deductionvalue = 0,
	$_modified,
	$_submitted;

	private function Validate(){
		if (isset($this->_store) AND isset($this->_source) AND isset($this->_user)) {
			return true;
		} else {
			return false;
		}
	}

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

	public function SetSource($source){
		$this->_source = $source;
	}

	public function GetSource(){
		return $this->_source;
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
		$this->_store = $store;
		$this->_user = $user;
	}

	public function SavedLast($store, $user) {
		$now = "'" . date("Y-m-d") . "%'";

		$sql = "SELECT modified
		FROM daily
		WHERE user_id_fk = $user
		AND store_id_fk = $store
		AND created LIKE $now
		ORDER BY modified DESC";

		$db = DB::Create();
		$query = $db->prepare($sql);
		$query->bindValue(':sid', $store);
		$query->bindValue(':now', $now);
		$query->bindValue(':uid', $user);
		$query->execute();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		$saved = new DateTime($result[0]['modified']);
		echo 'Last saved at ' . $saved->format("h:ia");

	}

	public function TotalRev($store){
		$now = "'" . date("Y-m-d") . "%'";

		$sql = "SELECT revenue_value as rev
		FROM daily
		WHERE store_id_fk = $store
		AND created LIKE $now";

		$db = DB::Create();
		$query = $db->prepare($sql);
		$query->bindValue(':sid', $store);
		$query->bindValue(':now', $now);
		$query->execute();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as $key => $value) {
			$revenue += $value['rev'];
		}

		echo "$" . number_format($revenue, 2, ".", ",");
	}

	public function TotalDed($store){
		$now = "'" . date("Y-m-d") . "%'";

		$sql = "SELECT deduction_value as ded
		FROM daily
		WHERE store_id_fk = $store
		AND created LIKE $now";

		$db = DB::Create();
		$query = $db->prepare($sql);
		$query->bindValue(':sid', $store);
		$query->bindValue(':now', $now);
		$query->execute();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as $key => $value) {
			$revenue += $value['ded'];
		}

		echo "$" . number_format($revenue, 2, ".", ",");
	}

	public function FetchSources($store = null){
		$sql = 'SELECT
		id_pk as id,
		source_id_fk as source
		FROM store_source
		WHERE store_id_fk = :store
		AND store_source_active = 1';

		$db = DB::Create();
		$query = $db->prepare($sql);
		if (is_null($store)) {
			$query->bindValue(':store', $this->_store);
		} else {
			$query->bindValue(':store', $store);
		}

		$query->execute();
		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		if (isset($result)){
			$results = array();
			foreach ($result as $key => $value) {
				$results[] = $value['source'];
			}
			return $results;
		} else {
			echo "Failed";
		}
	}

	public function InitDay(){
		$sources = self::CompareDate();
		self::WriteHTML($sources);
	}

	private function CompareDate(){
		$now = date("Y-m-d");
		$sql = 'SELECT id_pk as id, created as cdate, source_id_fk as source FROM daily WHERE user_id_fk = :uid AND store_id_fk = :sid AND created LIKE :now';

		$db = DB::Create();
		$query = $db->prepare($sql);
		$query->bindValue(':uid', $this->_user);
		$query->bindValue(':sid', $this->_store);
		$query->bindValue(':now', $now . '%');
		$query->execute();

		$source = $this->FetchSources();

		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		$out = array();

		if (!empty($result)) {
			foreach ($result as $key => $value) {
				$results[] = $value['source'];
			}
		} else {
			foreach ($source as $key => $value) {
				$this->_source = $value;
				$this->WriteTrans();
			}

			$query->execute();
			$result = $query->fetchAll(PDO::FETCH_ASSOC);

			foreach ($result as $key => $value) {
				$results[] = $value['source'];
			}
		}

		if (!empty($results)) {
			foreach ($source as $key => $value) {
				if (in_array($value, $results)) {
					$out[] = $result[$key]['id'];
				} else {
					$this->_source = $value;
					$this->WriteTrans();
					$out[] = $result[$key]['id'];
				}
			}
		}
		return $out;
	}

	public function WriteTrans(){
		if (self::Validate()){
			$sql = 'INSERT INTO daily (user_id_fk, source_id_fk, store_id_fk, revenue_value, deduction_value) VALUES (:user, :source, :store, :rval, :dval)';

			$db = DB::Create();
			$query = $db->prepare($sql);
			$query->bindValue(':user', $this->_user);
			$query->bindValue(':store', $this->_store);
			$query->bindValue(':source', $this->_source);
			$query->bindValue(':rval', $this->_revenuevalue);
			$query->bindValue(':dval', $this->_deductionvalue);
			$query->execute();
			return true;
		} else {
			return false;
		}
	}

	public function UpdateTrans(){

		$db = DB::Create();
		$rarr = array();
		$darr = array();

		if (!empty($_POST['formSave'])){
			unset($_POST['formSave']);
			ksort($_POST);
			foreach ($_POST as $key => $value) {
				if (substr($key, -1, 1) == "r"){
					$rid = rtrim($key, "r");
					$rval = $value;
					$sql = "
					UPDATE daily
					SET revenue_value = $rval
					WHERE id_pk=$rid";
					$query = $db->prepare($sql);
					$query->execute();
				} elseif (substr($key, -1, 1) == "d") {
					$did = rtrim($key, "d");
					$dval = $value;
					$sql = "
					UPDATE daily
					SET deduction_value = $dval
					WHERE id_pk=$did";
					$query = $db->prepare($sql);
					$query->execute();
				}
			}

		} elseif (!empty($_POST['formSubmit'])) {

		}


	}

	private function WriteHTML($src){
		$ids = implode(",", $src);

		$sql = '
		SELECT
		daily.id_pk as id,
		daily.revenue_value as revenue,
		daily.deduction_value as deduction,
		daily.submitted as submitted,
		daily.modified as modified,
		source.source_name as name
		FROM daily
		LEFT JOIN source
		ON daily.source_id_fk=source.id_pk
		WHERE daily.id_pk IN (' . $ids . ')
		ORDER BY
		source.source_name ASC';

		$db = DB::Create();
		$query = $db->prepare($sql);
		try{
			$query->execute();
		} catch (exception $e){

		}

		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		foreach ($result as $key => $value) {
			$name = $value['name'];
			$rev = number_format($value['revenue'], 2, '.', '');
			$ded = number_format($value['deduction'], 2, '.', '');
			$id = $value['id'];

			$this->_revenue .= "<div class=\"revenuesource\"><div><p>$name</p></div><div><input class =\"rinput\" type=\"text\" name=\"" . $id . "r\" id=\"" . $id . "r\" onkeypress=\"return isNumberKey(event)\" value=\"$rev\"></div></div>";
			$this->_deduction .= "<div class=\"deductionssource\"><div><p>$name</p></div><div><input class =\"dinput\" type=\"text\" name=\"" . $id . "d\" id=\"" . $id . "d\" onkeypress=\"return isNumberKey(event)\" value=\"$ded\"></div></div>";
		}
	}
}