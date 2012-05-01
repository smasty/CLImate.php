<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\App;

use CLImate\Arguments,
	CLImate\IO,
	CLImate\ApplicationException;


/**
 * CLImate Application infrastructure.
 * @todo Help
 * @todo Application-wide options
 * @todo Error-handling
 * @todo Tests
 */
class Application {


	/** @var array */
	private $commands = array();

	/** @var array */
	private $commandCache = array();

	/** @var string */
	private $mainCommand;


	/**
	 * Registers an application command.
	 * @param string $command Command name
	 * @param string $class Class for the command
	 * @return Application fluent interface
	 * @throws ApplicationException
	 */
	public function registerCommand($command, $class){
		if(isset($this->commands[$command]))
			throw new ApplicationException("Command '$command' already registered.");
		if(!is_subclass_of($class, 'CLImate\\App\\Command'))
			throw new ApplicationException("Cannot register command: '$class' is not a valid CLImate command.");
		$this->commands[$command] = $class;

		return $this;
	}


	/**
	 * Registers a main command - gets executed if no command was specified.
	 * @param string $command Command name
	 * @param string $class Class for the command
	 * @return Application fluent interface
	 */
	public function registerMainCommand($command, $class){
		return $this->registerCommand($command, $class)
			->setMainCommand($command);
	}


	/**
	 * Sets a command as a main application command.
	 * @param type $command Command name
	 * @return Application fluent interface
	 * @throws ApplicationException
	 */
	public function setMainCommand($command){
		if(!isset($this->commands[$command]))
			throw new ApplicationException("Cannot set '$command' as a main command, no such command registered.");

		$this->mainCommand = $command;

		return $this;
	}


	/**
	 * Runs the application with given command-line arguments.
	 * @param array $args Command-line arguments (e.g. $_SERVER['argv'])
	 * @return bool
	 */
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
	 * Returns the command class instance.
	 * @param string $command
	 * @return Command
	 */
	protected function getCommand($command){
		if(isset($this->commandCache[$command]))
			return $this->commandCache[$command];
		return $this->commandCache[$command] = new $this->commands[$command];
	}


	/**
	 * Shows a help message.
	 * @todo Help implementation
	 * @param string $error
	 * @return void
	 */
	protected function showHelp($error = null){
		if($error)
			IO::error("&r$error&N\n");
		IO::line("&yHelp...&N");
	}


}