<?php

require_once 'core/init.php';
$sid = session_id();
// echo "Session ID: " . $sid;

$coluser = array(
	'first_name' => 'David',
	'last_name' => 'Sorensen',
	'username' => 'TIJ',
	'password' => '123',
	'user_permissions_fk' => '2'
	);

$user = new DB;
//$user->insert("user", $coluser);
$result = $user->putUser("user", $coluser);
// var_dump($result);

