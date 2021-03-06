<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * CLI arguments handling.
 */
class Arguments {


	const VALUE_KEY = '__values__';


	/**
	 * Parse CLI arguments.
	 * @param array $args Arguments to parse
	 * @return array Parsed options
	 */
	public static function parseArguments(array $args){
		$options = array();
		while($opt = current($args)){
			if(
				// --option[=value]
				preg_match('~^--([a-z][-a-z0-9]*[a-z0-9])(?:=(.+))?$~i', $opt, $match) ||
				// -a[=value]
				preg_match('~^-([a-z0-9])(?:=(.+))?$~i', $opt, $match) ||
				// -abc[=value]
				preg_match('~^(-[a-z0-9]{2,})(?:=(.+))?$~i', $opt, $match)
			){
				$name = $match[1];

				if(!empty($match[2]))
					$value = $match[2];

				else{
					$next = next($args);
					if($next[0] == '-' || $next === false){
						$value = true;
						prev($args);
					} else
						$value = $next;
				}

				if($name[0] == '-'){
					foreach(str_split(substr($name, 1, -1)) as $n){
						$options[$n][] = true;
					}
					$name = substr($name, -1);
				}
				$options[$name][] = $value;
			}
			// only value, without name
			elseif(preg_match('~^(.+)$~i', $opt, $match)){
				$options[Arguments::VALUE_KEY][] = $match[1];
			}

			next($args);
		}

		array_walk($options, function(& $val, $key){
			$val = count($val) > 1 || $key === static::VALUE_KEY ? $val : $val[0];
		});

		return $options;
	}


}