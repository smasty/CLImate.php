<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers;

use CLImate,
	CLImate\IO;


/**
 * Notifier showing string of dots.
 * @todo printf named args
 */
class Dots extends CLImate\Notifier {


	/** @var string */
	protected $format = "{:msg} {:dots}  ({:elapsed}, {:speed}/s)";

	/** @var int */
	protected $dots;

	private $i = 0;


	/**
	 * Create the notificator.
	 * @param string $message Message to display next to dots
	 * @param int $dots Number of dots to iterate through
	 * @param int $interval Refresh interval in miliseconds
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function __construct($message, $dots = 3, $interval = 300){
		$this->dots = (int) $dots + 1;
		parent::__construct($message, $interval);
		if($dots <= 0)
			throw new \InvalidArgumentException('Number of dots must be positive.');
	}


	/**
	 * Set display format.
	 *
	 * Availble named arguments: `msg`, `dots`, `elapsed`, `speed`, `ticks`.
	 * Default is `{:msg} {:dots}  ({:elapsed}, {:speed}/s)`.
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

		$i = $this->last ? $this->dots-1 : $this->i++ % $this->dots;
		$dots = str_pad(str_repeat('.', $i), $this->dots);
		$args = compact('msg', 'dots', 'elapsed', 'speed', 'ticks');

		if($return)
			return IO::render($this->format, $args);
		
		IO::cr();
		IO::write($this->format, $args);
	}


}