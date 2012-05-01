<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


class Application {


	private $commands = array();

	private $commandCache = array();

	private $mainCommand;


	public function registerCommand($command, $class){
		if(isset($this->commands[$command]))
			throw new ApplicationException("Command '$command' already registered.");
		if(!is_subclass_of($class, 'CLImate\\Command'))
			throw new ApplicationException("Cannot register command: '$class' is not a valid CLImate command.");
		$this->commands[$command] = $class;

		return $this;
	}


	public function registerMainCommand($command, $class){
		return $this->registerCommand($command, $class)
			->setMainCommand($command);
	}


	public function setMainCommand($command){
		if(!isset($this->commands[$command]))
			throw new ApplicationException("Cannot set '$command' as a main command, no such command registered.");

		$this->mainCommand = $command;

		return $this;
	}


	public function run(array $args){
		array_shift($args);
		$cmd = current($args);
		if(!$cmd){
			if(!$this->mainCommand){
				$this->showHelp('No command specified.');
				return false;
			}
			$cmd = $this->mainCommand;
		}

		if(!isset($this->commands[$cmd]) && !$this->mainCommand){
			$this->showHelp("Command '$cmd' does not exist.");
			return false;
		}
		else
			array_shift($args);

		$args = Arguments::parseArguments($args);
		$command = $this->getCommand($cmd);
		$command->parseOptions($args);
		return $command->invoke();
	}


	/**
	 * @param string $command
	 * @return Command
	 */
	protected function getCommand($command){
		if(isset($this->commandCache[$command]))
			return $this->commandCache[$command];
		return $this->commandCache[$command] = new $this->commands[$command];
	}


	protected function showHelp($error = null){
		if($error)
			IO::error("&r$error&N\n");
		IO::line("&yHelp...&N");
	}


}