<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Input/Output handling.
 */
class IO {


	/** @var resource Standard output resource */
	public static $stdOut = STDOUT;

	/** @var resource Standard input resource */
	public static $stdIn = STDIN;

	/** @var resource Standard error resource */
	public static $stdErr = STDERR;


	/**
	 * Render given text.
	 * @param string $text
	 * @return string Rendered text
	 */
	public static function render($text){
		$args = func_get_args();

		$text = $args[0] = Color::colorize($text);

		if(count($args) == 1)
			return $text;

		if(is_array($args[1])){
			foreach($args[1] as $key => $val)
				$text = str_replace("{:$key}", $val, $text);
			return $text;
		}

		return call_user_func_array('sprintf', $args);
	}


	/**
	 * Write given text to standard output. Supports sprintf() syntax.
	 * @param string $text
	 * @return int|FALSE
	 */
	public static function write($text){
		return fwrite(static::$stdOut, call_user_func_array('static::render', func_get_args()));
	}


	/**
	 * Write given text to standard output and append a newline. Supports sprintf() syntax.
	 * @param string $text
	 */
	public static function line($text = ''){
		$args = func_get_args();
		$args[0] = $text ? "$text\n" : "\n";
		return call_user_func_array('static::write', $args);
	}


	/**
	 * Carriage return.
	 */
	public static function cr(){
		return static::write("\r");
	}


	/**
	 * Write given text to standard error output. Supports sprintf() syntax.
	 *
	 * Optionally can exit the program with the given error code.
	 * @param string $message Error message.
	 * @return int|FALSE
	 */
	public static function error($message){
		$args = func_get_args();
		array_shift($args);
		return fwrite(static::$stdErr, call_user_func_array('static::render', func_get_args()));
	}


	/**
	 * Read from standard input.
	 * @param string $format Format of input (see sscanf() for details).
	 * @return string
	 * @throws InputException If ^D is caught during input.
	 */
	public static function read($format = null){
		if($format)
			fscanf(static::$stdIn, $format . "\n", $line);
		else
			$line = fgets(static::$stdIn);

		if($line === false)
			throw new InputException('Caught ^D during input');

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
			if($input || $default !== null)
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
		}
	}


	/**
	 * Show the list of available options to choose from.
	 * @param Traversable|array $items Items to choose from.
	 * @param int $default Index of the default option.
	 * @param string $message Message to show under the list.
	 * @return int Index of the chosen option.
	 */
	public static function menu($items, $default = null, $message = 'Choose an option'){
		$items = $items instanceof \Traversable ? iterator_to_array($items) : $items;
		if(!is_array($items))
			throw new \InvalidArgumentException('Items must be an array or a Traversable instance.');

		// Keys might not be numeric
		$map = array_values($items);

		// Format options
		$size = strlen(count($map));
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


	/**
	 * Create a new table. See `Table::__construct()` for details on usage.
	 * @param \Traversable|array $header
	 * @param \Traversable[]|array[] $rows
	 * @return Table
	 */
	public static function table($header = null, $rows = null){
		return new Table($header, $rows);
	}


	/**
	 * Number of columns in terminal.
	 * @return int
	 * @todo Better solution maybe?
	 */
	public static function columns(){
		return (int) exec('/usr/bin/env tput cols');
	}


	/**
	 * Returns length of the string, taking into account color codes.
	 * @param string $string
	 * @return int
	 */
	public static function strlen($string){
		return Color::strlen($string);
	}


}