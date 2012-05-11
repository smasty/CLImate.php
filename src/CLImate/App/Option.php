<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\App;


/**
 * Command option in CLImate Application.
 */
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


	/**
	 * Constructs an option.
	 * @param bool $flag
	 * @param bool $valueOnly
	 * @param bool $multiValue
	 * @param string $longName
	 * @param string $shortName
	 * @param string $description
	 */
	public function __construct($flag, $valueOnly, $multiValue, $longName = null, $shortName = null, $description = null){
		$this->flag = (bool) $flag;
		$this->valueOnly = (bool) $valueOnly;
		$this->multiValue = (bool) $multiValue;
		$this->longName = $longName;
		$this->shortName = $shortName;
		$this->description = (string) $description;
	}


	/**
	 * Returns long name.
	 * @return string
	 */
	public function getLongName(){
		return $this->longName;
	}


	/**
	 * Returns short name.
	 * @return string
	 */
	public function getShortName(){
		return $this->shortName;
	}


	/**
	 * Returns name - long or short.
	 * @return string
	 */
	public function getName(){
		return $this->longName ?: $this->shortName;
	}


	/**
	 * Returns value.
	 * @return string
	 */
	public function getValue(){
		return $this->value ?: $this->defaultValue;
	}


	/**
	 * Returns allowed values.
	 * @return array
	 */
	public function getAllowedValues(){
		return $this->allowedValues;
	}


	/**
	 * Returns default value.
	 * @return string
	 */
	public function getDefaultValue(){
		return $this->defaultValue;
	}


	/**
	 * Returns description.
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}


	/**
	 * Returns value placeholder for help.
	 * @return string
	 */
	public function getPlaceholder(){
		return $this->placeholder ?: strtoupper($this->longName ?: 'value');
	}


	/**
	 * Is required?
	 * @return bool
	 */
	public function isRequired(){
		return $this->defaultValue !== null;
	}


	/**
	 * Is flag?
	 * @return bool
	 */
	public function isFlag(){
		return $this->flag;
	}


	/**
	 * Is value-only?
	 * @return bool
	 */
	public function isValueOnly(){
		return $this->valueOnly;
	}


	/**
	 * Is multi-value?
	 * @return bool
	 */
	public function isMultiValue(){
		return $this->multiValue;
	}


	/**
	 * Has long name?
	 * @return bool
	 */
	public function hasLongName(){
		return (bool) $this->longName;
	}


	/**
	 * Has short name?
	 * @return bool
	 */
	public function hasShortName(){
		return (bool) $this->shortName;
	}


	/**
	 * Has value?
	 * @return bool
	 */
	public function hasValue(){
		return (bool) $this->value;
	}


	/**
	 * Sets value.
	 * @param mixed $value
	 */
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


	/**
	 * Sets allowed values. Either an array or a list of arguments.
	 * @param array $allowedValues
	 */
	public function allow($allowedValues){
		$this->allowedValues = is_array($allowedValues) ? $allowedValues : func_get_args();
	}


	/**
	 * Sets default value.
	 * @param string $defaultValue
	 */
	public function defaultValue($defaultValue){
		$this->defaultValue = $defaultValue;
	}


	/**
	 * Sets description.
	 * @param string $description
	 */
	public function description($description){
		$this->description = $description;
	}


	/**
	 * Sets value placeholder for help.
	 * @param string $placeholder
	 */
	public function placeholder($placeholder){
		$this->placeholder = $placeholder;
	}


	/**
	 * Returns string respresenation of value, if possible.
	 * @return string
	 */
	public function __toString(){
		return $this->multiValue ? '' : (string) $this->value;
	}


}