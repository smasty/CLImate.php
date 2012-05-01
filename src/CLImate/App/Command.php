<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


namespace CLImate\App;

use CLImate\Arguments;


/**
 * Abstract ancestor for all CLImate Application commands.
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
	 * @param string $longName Long name of the option, e.g. `sort-order`
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Short name of the option (one letter), e.g. `s`
	 * @return Option
	 */
	protected function option($longName, $description = null, $shortName = null){
		$option = new Option(false, false, false, $longName, $shortName, $description);
		$this->options->setOption($option, $longName, $shortName);
		return $option;
	}


	/**
	 * Defines a short-name option for the command.
	 * @param string $shortName Short name of the option, e.g. `s`
	 * @param string $description Description of the option, will be shown in help
	 * @return Option
	 */
	protected function shortOption($shortName, $description = null){
		$option = new Option(false, false, false, null, $shortName, $description);
		$this->options->setOption($option, null, $shortName);
		return $option;
	}


	/**
	 * Defines a flag option for the command (can be `true` or `false`).
	 * @param string $longName Long name of the option, e.g. `sort-order`
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Short name of the option (one letter), e.g. `s`
	 * @return Option
	 */
	protected function flag($longName, $description = null, $shortName = null){
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
	 * @param string $longName Long name of the option, e.g. `files`
	 * @param string $description Description of the option, will be shown in help
	 * @param string $shortName Short name of the option (one letter), e.g. `f`
	 * @return Option
	 */
	protected function multiValueOption($longName, $description = null, $shortName = null){
		$option = new Option(false, false, true, $longName, $shortName, $description);
		$this->options->setOption($option, $longName, $shortName);
		return $option;
	}


}