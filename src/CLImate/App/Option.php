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

	/** @var bool */
	private $multiValue;

	/** @var string */
	private $longName;

	/** @var string */
	private $shortName;

	/** @var string */
	private $description;

	/** @var string */
	private $placeholder;

	/** @var mixed */
	private $value;

	/** @var array */
	private $allowedValues = array();

	/** @var mixed|null */
	private $defaultValue;


	public function __construct($flag, $valueOnly, $multiValue, $longName = null, $shortName = null, $description = null){
		$this->flag = (bool) $flag;
		$this->valueOnly = (bool) $valueOnly;
		$this->multiValue = (bool) $multiValue;
		$this->longName = $longName;
		$this->shortName = $shortName;
		$this->description = (string) $description;
	}


	public function getLongName(){
		return $this->longName;
	}


	public function getShortName(){
		return $this->shortName;
	}


	public function getValue(){
		return $this->value ?: $this->defaultValue;
	}


	public function getAllowedValues(){
		return $this->allowedValues;
	}


	public function getDefaultValue(){
		return $this->defaultValue;
	}


	public function getDescription(){
		return $this->description;
	}


	public function getPlaceholder(){
		return $this->placeholder ?: strtoupper($this->longName ?: 'value');
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


	public function isMultiValue(){
		return $this->multiValue;
	}


	public function hasLongName(){
		return (bool) $this->longName;
	}


	public function hasShortName(){
		return (bool) $this->shortName;
	}


	public function hasValue(){
		return (bool) $this->value;
	}


	public function setValue($value){
		if($this->flag)
			$this->value = (bool) $value;
		elseif(empty($this->allowedValues))
			$this->value = $value;
		else{
			if($this->multiValue){
				$this->value = array();
				foreach($value as $val)
					if(in_array($val, $this->allowedValues))
						$this->value[] = $val;
				return;
			}

			$this->value = in_array($value, $this->allowedValues) ? $value : null;
		}
	}


	public function allow($allowedValues){
		$this->allowedValues = is_array($allowedValues) ? $allowedValues : func_get_args();
	}


	public function defaultValue($defaultValue){
		$this->defaultValue = $defaultValue;
	}


	public function description($description){
		$this->description = $description;
	}


	public function placeholder($placeholder){
		$this->placeholder = $placeholder;
	}


	public function __toString(){
		return $this->multiValue ? '' : (string) $this->value;
	}


}