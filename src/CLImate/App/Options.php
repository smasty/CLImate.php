<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\App;


class Options implements \ArrayAccess, \IteratorAggregate {


	/** @var Option[] */
	private $options;

	/** @var Option[] */
	private $shortOptions;


	public function setOption(Option $option, $name = null, $shortName = null){
		if($name === null && $shortName === null)
			throw new \InvalidArgumentException('Either $name or $shortName must be specified.');
		if($name !== null)
			$this->options[$name] = $option;
		if($shortName !== null)
			$this->shortOptions[$shortName] = $option;

		return $this;
	}


	public function getOption($name){
		if(isset($this->options[$name]))
			return $this->options[$name];
		elseif(isset($this->shortOptions[$name]))
			return $this->shortOptions[$name];
	}


	public function offsetGet($offset){
		if(isset($this->options[$offset]))
			return $this->options[$offset]->getValue();
		elseif(isset($this->shortOptions[$offset]))
			return $this->shortOptions[$offset]->getValue();
	}


	public function offsetExists($offset){
		return isset($this->options[$offset]) || isset($this->shortOptions[$offset]);
	}


	public function offsetSet($offset, $value){
		if(isset($this->options[$offset]))
			$this->options[$offset]->setValue($value);
		if(isset($this->shortOptions[$offset]))
			$this->shortOptions[$offset]->setValue($value);
	}


	public function offsetUnset($offset){
		$this->offsetSet($offset, null);
	}


	public function __get($name){
		return $this->offsetGet($name);
	}


	public function __isset($name){
		return $this->offsetExists($name);
	}


	public function __set($name, $value){
		return $this->offsetSet($name, $value);
	}


	public function __unset($name){
		return $this->offsetUnset($name);
	}



	public function getIterator(){
		$it = new \AppendIterator;
		$it->append(new \ArrayIterator($this->options));
		$it->append(new \ArrayIterator($this->shortOptions));
		return $it;
	}


	public function getOptions(){
		return $this->getIterator();
	}


	public function getLong(){
		return new OptionIterator($this->getIterator(), function($opt){
			return $opt->hasLongName();
		});
	}


	public function getShort(){
		return new OptionIterator($this->getIterator(), function($opt){
			return $opt->hasShortName();
		});
	}


	public function getFlags(){
		return new OptionIterator($this->getIterator(), function($opt){
			return $opt->isFlag();
		});
	}


	public function getRequired(){
		return new OptionIterator($this->getIterator(), function($opt){
			return $opt->isRequired();
		});
	}


	public function getOptional(){
		return new OptionIterator($this->getIterator(), function($opt){
			return !$opt->isRequired();
		});
	}


	public function getValueOnly(){
		return new OptionIterator($this->getIterator(), function($opt){
			return $opt->isValueOnly();
		});
	}


	public function getNamed(){
		return new OptionIterator($this->getIterator(), function($opt){
			return !$opt->isValueOnly();
		});
	}


}



class OptionIterator extends \FilterIterator {

	/** @var callable */
	private $callback;

	public function __construct(\Iterator $iterator, $callback){
		if(!is_callable($callback))
			throw new \InvalidArgumentException('Parameter $callback not callable.');
		$this->callback = $callback;
		parent::__construct($iterator);
	}

	public function accept(){
		return call_user_func($this->callback, $this->current());
	}

}