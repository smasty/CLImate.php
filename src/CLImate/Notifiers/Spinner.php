<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers;

use CLImate,
	CLImate\IO;


/**
 * Basic ASCII spinner.
 */
class Spinner extends CLImate\Notifier {


	/** @var string */
	protected $format = "{:msg} {:spinner}  ({:elapsed}, {:speed}/s)";

	/** @var string */
	protected $chars = '-\|/';

	private $i = 0;


	/**
	 * Set display format.
	 *
	 * Availble named arguments: `msg`, `spinner`, `elapsed`, `speed`, `ticks`.
	 * Default is `{:msg} {:spinner}  ({:elapsed}, {:speed}/s)`.
	 * @param string $format
	 * @return void
	 */
	public function format($format){
		$this->format = $format;
	}


	/**
	 * Render the notificator.
	 * @param $return Return the notification instead of printing it.
	 * @return void|string
	 */
	public function display($return = false){
		$msg = $this->message;
		$elapsed = $this->formatTime($this->elapsed());
		$speed = round($this->speed());
		$ticks = number_format($this->iterator);

		$id = $this->i++ % strlen($this->chars);
		$spinner = $this->last ? ' ' : $this->chars[$id];
		$args = compact('msg', 'spinner', 'elapsed', 'speed', 'ticks');

		if($return)
			return IO::render($this->format, $args);

		IO::cr;
		IO::write($this->format, $args);
	}


}