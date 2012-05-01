<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */


namespace CLImate;


abstract class Command {


	protected $arguments;


	abstract public function invoke();


	public function parseOptions(array $arguments){
		$this->arguments = $arguments;
	}


}