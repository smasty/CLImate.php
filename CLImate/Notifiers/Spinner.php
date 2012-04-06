<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers;

use CLImate,
	CLImate\IO;


/**
 * Basic ASCII spinner.
 * @todo printf named args
 */
class Spinner extends CLImate\Notifier {


	/** @var string */
	protected $format = "{:msg} {:spinner}  ({:elapsed},  {:speed}/s)";

	/** @var string */
	protected $chars = '-\|/';

	private $i = 0;


	/**
	 * Render the notificator.
	 * @return void
	 */
	public function display(){
		$msg = $this->message;
		$elapsed = $this->formatTime($this->elapsed());
		$speed = round($this->speed());

		$id = $this->i++ % strlen($this->chars);
		$spinner = $this->last ? ' ' : $this->chars[$id];
		IO::write($this->format, compact('msg', 'spinner', 'elapsed', 'speed'));
	}


}