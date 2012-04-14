<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


spl_autoload_register(function($class){
	$class = trim($class, '\\');
	$file = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
	if(substr($class, 0, 8) === 'CLImate\\'){
		if(is_file($file))
			return include_once $file;
		elseif(substr($class, -9) === 'Exception')
			return include_once __DIR__ . '/CLImate/exceptions.php';
		return false;
	}
});