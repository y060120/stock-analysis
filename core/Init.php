<?php
	session_start();

	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => '127.0.0.1',
			'username' => 'crysis',
			'password' => 'root',
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
