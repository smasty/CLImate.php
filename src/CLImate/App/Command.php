<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


namespace CLImate\App;

use CLImate\Arguments,
	CLImate\IO,
	CLImate\AppException;


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
 *
 * @todo Help
 * @todo Error-handling
 * @todo Tests
 * @todo Unknown/required arguments check
 * @todo Do not pass parent arguments to children
 */
class Command {

	/** @var Options */
	protected $options;

	protected $scriptName;

	/** @var Command */
	private $parent;

	private $commands = array();

	private $commandCache = array();


	/**
	 * Invokes the command.
	 */
	public function invoke(){

	}


	/**
	 * Registers command options.
	 */
	public function registerOptions(){

	}


	/**
	 * Initializes the command.
	 */
	public function __construct(Command $parent = null){
		$this->options = new Options;
		$this->registerOptions();
		if($parent !== null)
			$this->parent = $parent;
	}


	public function __call($name, $args){
		if(method_exists('CLImate\\IO', $name)){
			return call_user_func_array(array('CLImate\\IO', $name), $args);
		} else
			throw new \BadMethodCallException("Call to undefined method " . get_class($this) . "::$name()");
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
	public function option($name, $description = null, $shortName = null){
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
	public function flag($name, $description = null, $shortName = null){
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
	public function valueOption($name, $description = null, $multiple = false){
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
	public function multiValueOption($name, $description = null, $shortName = null){
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


	public function addCommand($name, $class){
		if(isset($this->commands[$name]))
			throw new AppException("Command '$name' already registered.");
		if(!is_subclass_of($class, 'CLImate\\App\\Command'))
			throw new AppException("Cannot register command: '$class' is not a valid CLImate command.");
		$this->commands[$name] = $class;

		return $this;
	}


	/**
	 *
	 * @param string $name
	 * @return Command
	 */
	public function getCommand($name){
		if(isset($this->commandCache[$name]))
			return $this->commandCache[$name];
		return $this->commandCache[$name] = new $this->commands[$name]($this);
	}


	public function getParent(){
		return $this->parent;
	}


	public function hasCommands(){
		return !empty($this->commands);
	}


	public function run(array $argv){
		if($this->getParent() === null)
			$this->scriptName = array_shift($argv);

		if($this->hasCommands()){
			$cmd = current($argv);
			// Non-existent command
			if($cmd && $cmd{0} !== '-' && !isset($this->commands[$cmd]))
				throw new AppException("Command '$cmd' does not exist");

			// No command, arguments only or no arguments
			if(($cmd && $cmd{0} === '-') || !$cmd){
				$this->init($argv);
			}

			// Command
			if($cmd && isset($this->commands[$cmd])){
				array_shift($argv);
				$this->init($argv);
				$this->getCommand($cmd)->run($argv);
			}
		} else
			$this->init($argv);
	}


	private function init(array $argv){
		$arguments = Arguments::parseArguments($argv);
		$this->parseArguments($arguments);
		$this->invoke();
	}


	/**
	 * Parses given command-line arguments according to specified settings.
	 * @param array $arguments
	 */
	private function parseArguments(array $arguments){
		if(empty($arguments))
			return;

		foreach($this->options as $name => $option){
			if(isset($arguments[$name])){
				$option->setValue($arguments[$name]);
			}
		}

		if(isset($arguments[Arguments::VALUE_KEY]) && is_array($arguments[Arguments::VALUE_KEY]))
			foreach($this->options->getValueOnly() as $name => $option){
				if($option->isMultiValue()){
					$option->setValue($arguments[Arguments::VALUE_KEY]);
					break;
				}
				$option->setValue(current($arguments[Arguments::VALUE_KEY]));
				next($arguments[Arguments::VALUE_KEY]);
			}
	}


}