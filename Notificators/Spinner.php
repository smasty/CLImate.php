<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notificators;

use CLImate;


/**
 * Basic ASCII spinner.
 * @todo printf named args
 */
class Spinner extends CLImate\Notificator {


	/** @var string */
	protected $format = "%s %s  (%s,  %d/s)"; // {message} {spinner}  ({elapsed}, {speed}/s)

	/** @var string */
	protected $chars = '-\|/';

	private $i = 0;


	public function display(){
		$id = $this->i++ % strlen($this->chars);
		$spinner = $this->last ? ' ' : $this->chars[$id];
		printf($this->format, $this->message, $spinner, $this->formatTime($this->elapsed()), round($this->speed()));
	}


}