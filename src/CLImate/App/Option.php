<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\App;


class Option {


	/** @var bool */
	private $flag;

	/** @var bool */
	private $valueOnly;

	/** @var string */
	private $name;

	/** @var string */
	private $shortName;

	/** @var string */
	private $description;

	/** @var mixed */
	private $value;

	/** @var array */
	private $allowedValues = array();

	/** @var mixed|null */
	private $defaultValue;


	public function __construct($flag, $valueOnly, $name = null, $shortName = null, $description = null){
		$this->flag = (bool) $flag;
		$this->valueOnly = (bool) $valueOnly;
		$this->name = $name;
		$this->shortName = $shortName;
		$this->description = (string) $description;
	}


	public function getName(){
		return $this->name;
	}


	public function setName($name){
		$this->name = $name;
	}


	public function hasLongName(){
		return (bool) $this->name;
	}


	public function getShortName(){
		return $this->shortName;
	}


	public function setShortName($shortName){
		$this->shortName = $shortName;
	}


	public function hasShortName(){
		return (bool) $this->shortName;
	}


	public function getValue(){
		return $this->value ?: $this->defaultValue;
	}


	public function setValue($value){
		if($this->flag)
			$this->value = (bool) $value;
		elseif(!empty($this->allowedValues))
			$this->value = in_array($value, $this->allowedValues) ? $value : null;
		else
			$this->value = $value;
	}


	public function getAllowedValues(){
		return $this->allowedValues;
	}


	public function allow($allowedValues){
		$this->allowedValues = is_array($allowedValues) ? $allowedValues : func_get_args();
	}


	public function getDefaultValue(){
		return $this->defaultValue;
	}


	public function defaultValue($defaultValue){
		$this->defaultValue = $defaultValue;
	}


	public function isRequired(){
		return $this->defaultValue !== null;
	}


	public function isFlag(){
		return $this->flag;
	}


	public function isValueOnly(){
		return $this->valueOnly;
	}


	public function getDescription(){
		return $this->description;
	}


	public function setDescription($description){
		$this->description = $description;
	}


	public function __toString(){
		return is_scalar($this->value) ? (string) $this->value : '';
	}


}