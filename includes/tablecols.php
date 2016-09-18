<?php

$GLOBALS['coluser'] = array(
	'first_name' => NULL,
	'last_name' => NULL,
	'username' => NULL,
	'password' => NULL,
	'user_permissions_fk' => NULL
	);

$GLOBALS['colstore'] = array(
	'store_name' => NULL,
	'template' => NULL
	);

$GLOBALS['coltranstype'] = array(
	'type' => NULL
	);

$GLOBALS['coltrans'] = array(
	'transaction_type_fk' => NULL,
	'timestamp' => NULL,
	'store_id_fk' => NULL,
	'user_id_fk' => NULL,
	'amount' => NULL
	);

$GLOBALS['colstoreuser'] = array(
	'store_id_fk' => NULL,
	'user_id_fk' => NULL
	);

