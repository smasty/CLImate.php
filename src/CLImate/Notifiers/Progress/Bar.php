<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers\Progress;

use CLImate,
	CLImate\IO;


/**
 * Basic ASCII progress bar.
 */
class Bar extends CLImate\Notifiers\Progress {


	/** @var string */
	protected $formatBefore = '{:msg} {:percent}% [';

	/** @var string */
	protected $formatAfter = '] {:time}';

	/** @var string */
	protected $bar = '=>';


	/**
	 * Set display format.
	 *
	 * Availble named arguments: `msg`, `percent`, `time`, `elapsed`, `remaining`, `estimated`, `ticks`, `total`.
	 * `time` shows `remaining` during ticking and `elapsed` when finished.
	 * @param string $bar Format of the bar. Default is `=>`. Use only two characters.
	 * @param string $before Format of the part before the bar. Default is `{:msg} {:percent}% [`.
	 * @param string $after Format of the part after the bar. Default is `] {:time}`.
	 * @return void
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
	 * @param $return Return the notifier insted of printing it.
	 * @return void|string
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
		$bar = str_repeat($this->bar[0], floor($size * $this->percentage())) . $this->bar[$this->last ? 0 : 1];
		$bar = substr(str_pad($bar, $size), 0, $size);

		if($return)
			return IO::render($before . $bar . $after);

		IO::cr();
		IO::write($before . $bar . $after);
	}


}