<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


namespace CLImate\App;

use CLImate\Arguments;


abstract class Command {


	protected $arguments;


	/** @var Options */
	protected $options;


	public function __construct(){
		$this->options = new Options;
	}


	abstract public function invoke();


	public function parseOptions(array $arguments){
		$this->arguments = $arguments;

		foreach($this->options as $name => $option){
			if(isset($arguments[$name])){
				$option->setValue($arguments[$name]);
			}
		}

		if(is_array($arguments[Arguments::VALUE_KEY]))
			foreach($this->options->getValueOnly() as $name => $option){
				$option->setValue(current($arguments[Arguments::VALUE_KEY]));
				next($arguments[Arguments::VALUE_KEY]);
			}
	}


	public function getOption($name){
		return $this->options->getOption($name);
	}


	protected function option($name, $description = null, $shortName = null){
		$option = new Option(false, false, $name, $shortName, $description);
		$this->options->setOption($option, $name, $shortName);
		return $option;
	}


	protected function shortOption($shortName, $description = null){
		$option = new Option(false, false, null, $shortName, $description);
		$this->options->setOption($option, null, $shortName);
		return $option;
	}


	protected function flag($name, $description = null, $shortName = null){
		$option = new Option(true, false, $name, $shortName, $description);
		$this->options->setOption($option, $name, $shortName);
		return $option;
	}


	protected function valueOption($name, $description = null){
		$option = new Option(false, true, $name, null, $description);
		$this->options->setOption($option, $name);
		return $option;
	}


}