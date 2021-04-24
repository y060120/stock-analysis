<?php
	session_start();

	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => 'localhost',
			'username' => 'root',
			'password' => '',
			'db' => 'stock'
		),
		'session' => array(
			'session_name' => 'user',
			'token_name' => 'token'
		)
	);

	//autoloods the class that is used
	spl_autoload_register(function($class) {
		require_once 'classes/' . $class . '.php';
	});
?>
