<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notificators;

use CLImate,
	CLImate\IO;


/**
 * Notificator showing string of dots.
 * @todo printf named args
 */
class Dots extends CLImate\Notificator {


	/** @var string */
	protected $format = "%s %s  (%s,  %d/s)"; // {message} {dots}  ({elapsed}, {speed}/s)

	/** @var int */
	protected $dots;

	private $i = 0;


	public function __construct($message, $dots = 3, $interval = 300){
		$this->dots = (int) $dots + 1;
		parent::__construct($message, $interval);
		if($dots <= 0)
			throw new \InvalidArgumentException('Number of dots must be positive.');
	}


	public function display(){
		$i = $this->last ? $this->dots-1 : $this->i++ % $this->dots;
		$dots = str_pad(str_repeat('.', $i), $this->dots);
		IO::write($this->format, $this->message, $dots, $this->formatTime($this->elapsed()), round($this->speed()));
	}


}