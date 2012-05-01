<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


namespace CLImate\App;

use CLImate\Arguments,
	CLImate\IO;


/**
 * Abstract ancestor for all CLImate Application commands.
 * @method choose() string choose(string $question, string|array $choices, string $default)
 * @method columns() int columns()
 * @method cr() void cr()
 * @method error() int|FALSE error(string $message)
 * @method line() void line(string)
 * @method menu() int menu(array|Traversable $items, int $default, string $message)
 * @method prompt() string prompt(strng $question, string $default, string $ending)
 * @method read() string read(string $format)
 * @method render() string render(string $text)
 * @method strlen() int strlen(string $string)
 * @method table() CLImate\Table table(array|Traversable $header, array|Traversable $rows)
 * @method write() int|FALSE write(string $text)
 */
abstract class Command {


	/** @var array given command-line arguments in raw format. */
	protected $arguments;


	/** @var Options */
	protected $options;


	/**
	 * Initializes the command.
	 * @return void
	 */
	public function __construct(){
		$this->options = new Options;
	}


	/**
	 * Invokes the command.
	 * @return void
	 */
	abstract public function invoke();


	/**
	 * Parses given command-line arguments according to specified setting.
	 * @param array $arguments
	 * @return void
	 */
	public function parseOptions(array $arguments){
		$this->arguments = $arguments;

		foreach($this->options as $name => $option){
			if(isset($arguments[$name])){
				$option->setValue($arguments[$name]);
			}
		}

		if(is_array($arguments[Arguments::VALUE_KEY]))
			foreach($this->options->getValueOnly() as $name => $option){
				if($option->isMultiValue()){
					$option->setValue($arguments[Arguments::VALUE_KEY]);
					break;
				}
				$option->setValue(current($arguments[Arguments::VALUE_KEY]));
				next($arguments[Arguments::VALUE_KEY]);
			}
	}


	/**
	 * Returns the option by name as an Option instance.
	 * @param string $name
	 * @return Option
	 */
	public function getOption($name){
		return $this->options->getOption($name);
	}


	/**
	 * Defines an option for the command.
	 * @param string $name Long or short (one letter) name of the option
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Optional short name, if the first argument is a long name
	 * @return Option
	 */
	protected function option($name, $description = null, $shortName = null){
		if(strlen($name) === 1){
			$shortName = $name;
			$longName = null;
		}
		else
			$longName = $name;
		$option = new Option(false, false, false, $longName, $shortName, $description);
		$this->options->setOption($option, $longName, $shortName);
		return $option;
	}


	/**
	 * Defines a flag option for the command (can be `true` or `false`).
	 * @param string $name Long or short (one letter) name of the option
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Optional short name, if the first argument is a long name
	 * @return Option
	 */
	protected function flag($name, $description = null, $shortName = null){
		if(strlen($name) === 1){
			$shortName = $name;
			$longName = null;
		}
		else
			$longName = $name;
		$option = new Option(true, false, false, $longName, $shortName, $description);
		$this->options->setOption($option, $longName, $shortName);
		return $option;
	}


	/**
	 * Defines a name-less option for the command. E.g. for the `FILE` options.
	 * @param string $name Internal name of the option
	 * @param string $description Description of the option, will be shown in help
	 * @param bool $multiple Allow multiple values for the option (returned as an array)
	 * @return Option
	 */
	protected function valueOption($name, $description = null, $multiple = false){
		$option = new Option(false, true, $multiple, $name, null, $description);
		$this->options->setOption($option, $name);
		return $option;
	}


	/**
	 * Defines a multiple-value option for the command.
	 * @param string $name Long or short (one letter) name of the option
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Optional short name, if the first argument is a long name
	 * @return Option
	 */
	protected function multiValueOption($name, $description = null, $shortName = null){
		if(strlen($name) === 1){
			$shortName = $name;
			$longName = null;
		}
		else
			$longName = $name;
		$option = new Option(false, false, true, $longName, $shortName, $description);
		$this->options->setOption($option, $longName, $shortName);
		return $option;
	}


	public function __call($name, $args){
		if(method_exists('CLImate\\IO', $name)){
			return call_user_func_array(array('CLImate\\IO', $name), $args);
		}
	}


}