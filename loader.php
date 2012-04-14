<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


spl_autoload_register(function($class){
	$class = trim($class, '\\');
	$file = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
	if(substr($class, 0, 8) === 'CLImate\\' && is_file($file)){
		include_once $file;
	}
});