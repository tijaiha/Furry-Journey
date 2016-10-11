<?php
session_start();
spl_autoload_register(function($class){
	require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';