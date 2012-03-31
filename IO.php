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
	 * @param string $resource Defaults to STDOUT.
	 * @return void
	 */
	public function render($text, $resource = null){
		fwrite($resource ?: STDOUT, $text);
	}


	/**
	 * Write given text to standard output. Supports sprintf() syntax.
	 * @param string $text
	 * @return void
	 */
	public function write($text){
		return $this->render(call_user_func_array('sprintf', func_get_args()), STDOUT);
	}


	/**
	 * Write given text to standard output and append a newline. Supports sprintf() syntax.
	 * @param string $text
	 * @return void
	 */
	public function line($text = ''){
		$args = func_get_args();
		$args[0] = isset($args[0]) ? "$args[0]\n" : "\n";
		return call_user_func_array(array($this, 'write'), $args);
	}


	/**
	 * Write given text to standard error output. Supports sprintf() syntax.
	 *
	 * Optionally can exit the program with the given error code.
	 * @param string $message Error message.
	 */
	public function error($message){
		return $this->render(call_user_func_array('sprintf', func_get_args()), STDERR);
	}


	/**
	 * Read from standard input.
	 * @param string $format Format of input (see sscanf() for details).
	 * @return string
	 * @throws \Exception If ^D is caught during input.
	 */
	public function read($format = null){
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
	public function prompt($question, $default = null, $ending = ': '){
		if($default && strpos($question, '[') === false)
			$question .= " [$default]";

		while(true){
			$this->write($question . $ending);
			$input = $this->read();
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
	public function choose($question, $choices = 'yn', $default = 'n'){
		if(is_array($choices))
			$choices = implode('', $choices);

		// Lowercase options, uppercase default.
		$choices = str_replace($default, strtoupper($default), strtolower($choices));

		// Separate choices by slash.
		$choices = join('/', str_split($choices));

		while(true){
			$input = $this->prompt(sprintf("%s [%s] ", $question, $choices), $default, '');

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
	public function menu(array $items, $default = null, $message = 'Choose an option'){
		// Keys might not be numeric
		$map = array_values($items);

		// Format options
		$size = strlen(count($map)+1);
		foreach($map as $k => $v){
			$this->line("  %{$size}d. %s", ++$k, (string) $v);
		}
		$this->line();

		while(true){
			$input = $this->prompt($message, $default);

			if(is_numeric($input)){
				if(isset($map[--$input]))
					return array_search($map[$input], $items);

				if($input < 0 || $input >= count($map))
					$this->error("Menu selection out of range.\n");
			}
		}
	}


}