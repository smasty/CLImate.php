<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers\Progress;

use CLImate,
	CLImate\IO,
	CLImate\Notifiers;


/**
 * ASCII progress bar with spinner: |=====  {spinner}
 */
class SpinnerBar extends Notifiers\Progress {


	/** @var string */
	protected $formatBefore = '{:msg} |';

	/** @var string */
	protected $formatAfter = ' {:percent}% ({:time})';

	/** @var string */
	protected $bar = '=';

	/** @var Notifiers\Spinner */
	private $spinner;


	public function __construct($message, $total, $interval = 100){
		parent::__construct($message, $total, $interval);
		$this->spinner = new CLImate\Notifiers\Spinner('', $interval);
		$this->spinner->format('{:spinner}');
	}


	/**
	 * Set display format.
	 *
	 * Availble named arguments: `msg`, `percent`, `time`, `elapsed`, `remaining`, `estimated`, `ticks`, `total`.
	 * `time` shows `remaining` during ticking and `elapsed` when finished.
	 * @param string $bar Format of the bar. Default is `=`. Use only one character.
	 * @param string $before Format of the part before the bar. Default is `{:msg} |`.
	 * @param string $after Format of the part after the bar. Default is ` {:percent}% ({:time})`.
	 */
	public function format($bar = null, $before = null, $after = null){
		if($bar !== null)
			$this->bar = $bar;
		if($before !== null)
			$this->formatBefore = $before;
		if($after !== null)
			$this->formatAfter = $after;
	}


	/**
	 * Display progress bar.
	 * @param $return Return the notifier instead of printing it.
	 * @return null|string
	 */
	public function display($return = false){
		$elapsed = $this->elapsed();
		$estimated = $this->estimate();
		$remaining = $estimated - $elapsed;

		$msg = $this->message;
		$percent = str_pad(floor($this->percentage() * 100), 3, ' ', STR_PAD_LEFT);
		$time = $this->formatTime($this->last ? $elapsed : $remaining);
		$elapsed = $this->formatTime($elapsed);
		$estimated = $this->formatTime($estimated);
		$remaining = $this->formatTime($remaining);
		$total = number_format($this->total);
		$ticks = str_pad(number_format($this->iterator), strlen($total), ' ', STR_PAD_LEFT);
		$args = compact('msg', 'percent', 'elapsed', 'estimated', 'time', 'remaining', 'ticks', 'total');

		$before = IO::render($this->formatBefore, $args);
		$after = IO::render($this->formatAfter, $args);

		$cols = IO::columns();
		$size = $cols - IO::strlen($before . $after);
		$bar = str_repeat($this->bar, floor($size * $this->percentage() - ($this->last?1:0)));
		$bar = substr(str_pad($bar, $size - 1) . $this->getSpinnerState(), 0, $size);

		if($return)
			return IO::render($before . $bar . $after);

		IO::cr();
		IO::write($before . $bar . $after);
	}


	/**
	 * Get the inner spinner.
	 * @return Notifiers\Spinner
	 */
	public function getInnerSpinner(){
		return $this->spinner;
	}


	public function tick($ticks = 1){
		parent::tick($ticks);
		$this->spinner->increment($ticks);
	}


	/**
	 * Get spinner state for current display.
	 * @return string
	 */
	protected function getSpinnerState(){
		return $this->last ? '|' : $this->spinner->display(true);
	}

}