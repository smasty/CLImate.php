<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\App;



/**
 * CLImate Aplication option container.
 */
class Options implements \ArrayAccess, \IteratorAggregate {


	/** @var Option[] */
	private $longOptions;

	/** @var Option[] */
	private $shortOptions;


	/**
	 * Sets an option for the command.
	 * @param Option $option
	 * @param string $longName
	 * @param string $shortName
	 * @return Options fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setOption(Option $option, $longName = null, $shortName = null){
		if($longName === null && $shortName === null)
			throw new \InvalidArgumentException('Either $name or $shortName must be specified.');
		if($longName !== null)
			$this->longOptions[$longName] = $option;
		if($shortName !== null)
			$this->shortOptions[$shortName] = $option;

		return $this;
	}


	/**
	 * Returns an option by name.
	 * @param string $name
	 * @return Option
	 */
	public function getOption($name){
		if(isset($this->longOptions[$name]))
			return $this->longOptions[$name];
		elseif(isset($this->shortOptions[$name]))
			return $this->shortOptions[$name];
	}


	public function isEmpty(){
		return $this->longOptions == array() && $this->shortOptions == array();
	}


	/**
	 * Returns a value of an option.
	 * @param string $name
	 * @return mixed
	 */
	public function offsetGet($name){
		if(isset($this->longOptions[$name]))
			return $this->longOptions[$name]->getValue();
		elseif(isset($this->shortOptions[$name]))
			return $this->shortOptions[$name]->getValue();
	}


	/**
	 * Returns whether an option with a given name exists or not.
	 * @param string $name
	 * @return bool
	 */
	public function offsetExists($name){
		return isset($this->longOptions[$name]) || isset($this->shortOptions[$name]);
	}


	/**
	 * Sets a value for an option.
	 * @param string $name
	 * @param mixed $value
	 */
	public function offsetSet($name, $value){
		if(isset($this->longOptions[$name]))
			$this->longOptions[$name]->setValue($value);
		if(isset($this->shortOptions[$name]))
			$this->shortOptions[$name]->setValue($value);
	}


	/**
	 * Unsets a value of an option.
	 * @param string $name
	 */
	public function offsetUnset($name){
		$this->offsetSet($name, null);
	}


	/**
	 * Returns a value of an option.
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name){
		return $this->offsetGet($name);
	}


	/**
	 * Returns whether an option with a given name exists or not.
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name){
		return $this->offsetExists($name);
	}


	/**
	 * Sets a value for an option.
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value){
		return $this->offsetSet($name, $value);
	}


	/**
	 * Unsets a value of an option.
	 * @param string $name
	 */
	public function __unset($name){
		return $this->offsetUnset($name);
	}



	/**
	 * Returns an iterator above all the options.
	 * @return \AppendIterator
	 */
	public function getIterator(){
		$it = new \AppendIterator;
		if(!empty($this->longOptions))
			$it->append(new \ArrayIterator($this->longOptions));
		if(!empty($this->shortOptions))
			$it->append(new \ArrayIterator($this->shortOptions));
		return $it;
	}


	/**
	 * Returns options optionally filtered by given callback.
	 * @param callable $callback
	 * @return \AppendIterator|\CallbackFilterIterator
	 */
	public function getOptions($callback = null){
		if(is_callable($callback))
			return $this->buildIterator($this->getIterator(), $callback);
		return $this->getIterator();
	}


	/**
	 * Returns options with long names.
	 * @return \CallbackFilterIterator
	 */
	public function getLong(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return $opt->hasLongName();
		});
	}


	/**
	 * Returns options with short names.
	 * @return \CallbackFilterIterator
	 */
	public function getShort(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return $opt->hasShortName();
		});
	}


	/**
	 * Returns only flag options.
	 * @return \CallbackFilterIterator
	 */
	public function getFlags(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return $opt->isFlag();
		});
	}


	/**
	 * Returns only required options (without default value).
	 * @return \CallbackFilterIterator
	 */
	public function getRequired(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return $opt->isRequired();
		});
	}


	/**
	 * Returns only optional options (with default value).
	 * @return \CallbackFilterIterator
	 */
	public function getOptional(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return !$opt->isRequired();
		});
	}


	/**
	 * Returns value-only options.
	 * @return \CallbackFilterIterator
	 */
	public function getValueOnly(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return $opt->isValueOnly();
		});
	}


	/**
	 * Returns only named options (opposite of value-only).
	 * @return \CallbackFilterIterator
	 */
	public function getNamed(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return !$opt->isValueOnly();
		});
	}


	/**
	 * Returns only multi-value options.
	 * @return \CallbackFilterIterator
	 */
	public function getMultiValue(){
		return $this->buildIterator($this->getIterator(), function($opt){
			return !$opt->isMultiValue();
		});
	}


	/**
	 * Build a CallbackFilterIterator depending to the PHP version.
	 * @param \Iterator $itertator
	 * @param callable $callback
	 * @return \CallbackFilterIterator
	 */
	private function buildIterator(\Iterator $itertator, $callback){
		if(PHP_VERSION_ID >= 50400)
			return new \CallbackFilterIterator($itertator, $callback);
		return new CallbackFilterIterator_PHP53($itertator, $callback);

	}


}



if(PHP_VERSION_ID < 50400){

	/** @internal */
	class CallbackFilterIterator_PHP53 extends \FilterIterator {

		private $callback;

		public function __construct(\Iterator $iterator, $callback){
			if(!is_callable($callback))
				throw new \InvalidArgumentException('Invalid callback');
			$this->callback = $callback;
			parent::__construct($iterator);
		}

		public function accept(){
			return call_user_func($this->callback, $this->current(), $this->key(), $this);
		}

	}
}