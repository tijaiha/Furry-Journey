<?php

function CreateMenu($role, $id) {

	switch ($role) {
		case 1:
		echo '<h1>Admin Menu</h1><br>
		<p>Daily Sheets</p><br>'
		. '<ul style="storelist">' . GetStores($id) . '</ul>' .
		'<a href="index.php?page=users"><p>Manage Users</p></a><br>
		<a href="index.php?page=stores"><p>Manage Stores</p></a><br>
		<a href="index.php?page=sources"><p>Manage Sources</p></a><br>
		';
		break;
		case 2:
		echo '<h1>Manager Menu</h1><br>
		<p>Daily Sheets</p><br>'
		. '<ul style="storelist">' . GetStores($id) . '</ul>' .
		'<a href="index.php?page=report"><p>Reports</p></a><br>
		';
		break;
		case 3:
		echo '<h1>Clerk Menu</h1><br>
		<p>Daily Sheets</p><br>'
		. '<ul style="storelist">' . GetStores($id) . '</ul>'
		;
		break;

		default:
		echo '';
	}
	echo '<a href="index.php?page=logout"><p>Log Out</p></a><br>';
}

function SetPageTitle($page) {
	switch ($page) {
		case users:
		echo 'Manage Users';
		break;

		case daily:
		echo 'Daily Balance Sheet';
		break;

		case stores:
		echo 'Manage Stores';
		break;

		case sources:
		echo 'Manage Sources';
		break;

		default:
		echo 'Daily Balance Sheet';

	}
}

function GetStores($id) {
	$db = new DB();
	$db = $db->connect();

	$sql = '
	SELECT
	store_user.store_id_fk as store_id,
	store_user.user_id_fk as user,
	store.store_name as store_name
	FROM store_user
	LEFT JOIN store
	ON store_user.store_id_fk=store.id_pk
	WHERE store.store_active = "1" AND store_user.user_id_fk ="' . $id . '"';

	$query = $db->prepare($sql);
	$query->execute();
	$result = $query->fetchAll(PDO::FETCH_ASSOC);
	$return = '';
	foreach ($result as $key => $value) {
		$return .= '<li><a href="index.php?page=daily&s=' . $value['store_id'] . '">' . $value['store_name'] . '</a></li>';
	}
	return $return;
}

function StoreName($store) {
	$db = new DB();
	$db = $db->connect();

	$sql = '
	SELECT store_name as name
	FROM store
	WHERE id_pk = "' . $store . '"';

	$query = $db->prepare($sql);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);

	return $result['name'];
}

function StoreAuth() {
	$db = new DB();
	$db = $db->connect();
	$query = $db->query('SELECT store_id_fk as store_id FROM store_user WHERE user_id_fk="' . $_SESSION['user_id'] . '"');
	$result = $query->fetchAll(PDO::FETCH_ASSOC);

	foreach ($result as $key => $value) {
		foreach ($value as $key => $value) {
			$storeauth[] = $value;
		}
	}
	$_SESSION['storeauth'] = $storeauth;
}