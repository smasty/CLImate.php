<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Input/Output handling.
 */
class IO {


	/**
	 * Render given text into a resource.
	 * @param string $text
	 * @param resource $resource
	 * @param array $args
	 * @return void
	 */
	public static function render($text, $resource, array $args = array()){
		if(isset($args[0]) && is_array($args[0])){
			$named = $args[0];
			array_shift($args);
		}

		// sprintf() arguments
		$text = vsprintf($text, $args);

		// Named arguments
		if(isset($named)){
			$replacements = array();
			foreach($named as $k => $v)
				$replacements["{:$k}"] = $v;

			$text = strtr($text, $replacements);
		}

		fwrite($resource, $text);
	}


	/**
	 * Write given text to standard output. Supports sprintf() syntax.
	 * @param string $text
	 * @return void
	 */
	public static function write($text){
		$args = func_get_args();
		array_shift($args);
		return static::render($text, STDOUT, $args);
	}


	/**
	 * Write given text to standard output and append a newline. Supports sprintf() syntax.
	 * @param string $text
	 * @return void
	 */
	public static function line($text = ''){
		$args = func_get_args();
		$args[0] = $text ? "$text\n" : "\n";
		return call_user_func_array('static::write', $args);
	}


	/**
	 * Carriage return.
	 * @return void
	 */
	public static function cr(){
		return static::write("\r");
	}


	/**
	 * Write given text to standard error output. Supports sprintf() syntax.
	 *
	 * Optionally can exit the program with the given error code.
	 * @param string $message Error message.
	 */
	public static function error($message){
		$args = func_get_args();
		array_shift($args);
		return static::render($text, STDERR, $args);
	}


	/**
	 * Read from standard input.
	 * @param string $format Format of input (see sscanf() for details).
	 * @return string
	 * @throws \Exception If ^D is caught during input.
	 */
	public static function read($format = null){
		if($format)
			fscanf(STDIN, $format . "\n", $line);
		else
			$line = fgets(STDIN);

		if($line === false)
			throw new \Exception('Caught ^D during input');

		return trim($line);
	}


	/**
	 * Prompt the user to answer the question.
	 *
	 * If no input is given and no default value is defined, will
	 * ask the user again.
	 * @param string $question
	 * @param string $default Default value if user does not answer.
	 * @param string $ending A string to append to the question.
	 * @return string
	 */
	public static function prompt($question, $default = null, $ending = ': '){
		if($default && strpos($question, '[') === false)
			$question .= " [$default]";

		while(true){
			static::write($question . $ending);
			$input = static::read();
			if($input || $default)
				return $input ?: $default;
		}
	}


	/**
	 * Ask the user to choose one from offered choices (case insensitive).
	 * @param string $question
	 * @param string|array $choices List of one-letter choices.
	 * @param string $default Default choice, will be displayed uppercased.
	 * @return string
	 */
	public static function choose($question, $choices = 'yn', $default = 'n'){
		if(is_array($choices))
			$choices = implode('', $choices);

		// Lowercase options, uppercase default.
		$choices = str_replace($default, strtoupper($default), strtolower($choices));

		// Separate choices by slash.
		$choices = implode('/', str_split($choices));

		while(true){
			$input = static::prompt(sprintf("%s [%s] ", $question, $choices), $default, '');

			if(stripos($choices, $input) !== false)
				return strtolower($input);
			elseif(!$input && $default)
				return strtolower($default);
		}
	}


	/**
	 * Show the list of available options to choose from.
	 * @param string[] $items Items to choose from.
	 * @param int $default Index of the default option.
	 * @param string $message Message to show under the list.
	 * @return int Index of the chosen option.
	 */
	public static function menu(array $items, $default = null, $message = 'Choose an option'){
		// Keys might not be numeric
		$map = array_values($items);

		// Format options
		$size = strlen(count($map)+1);
		foreach($map as $k => $v){
			static::line("  %{$size}d. %s", ++$k, (string) $v);
		}
		static::line();

		while(true){
			$input = static::prompt($message, $default);

			if(is_numeric($input)){
				if(isset($map[--$input]))
					return array_search($map[$input], $items);

				if($input < 0 || $input >= count($map))
					static::error("Menu selection out of range.\n");
			}
		}
	}


}