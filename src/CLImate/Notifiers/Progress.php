<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Notifiers;

use CLImate,
	CLImate\IO;


/**
 * Abstract class for progress notifiers.
 */
abstract class Progress extends CLImate\Notifier {


	/** @var int */
	protected $total;


	/**
	 * Create progress notifier.
	 * @param string $message Message to display
	 * @param int $total  Total number of ticks to be performed
	 * @param int $interval Refresh interval
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function __construct($message, $total, $interval = 100){
		$this->total = (int) $total;
		parent::__construct($message, $interval);

		if($total <= 0)
			throw new \InvalidArgumentException('Total number of ticks must be positive.');
	}


	/**
	 * Total number of ticks to perform.
	 * @return int
	 */
	public function total(){
		return $this->total;
	}


	/**
	 * Estimate the time needed to complete.
	 * @return int Time in seconds
	 */
	public function estimate(){
		$speed = $this->speed();
		if($speed === 0 || $this->elapsed() === 0)
			return 0;

		return round($this->total / $speed);
	}


	/**
	 * Percentage completed.
	 * @return float
	 */
	public function percentage(){
		return $this->iterator / $this->total;
	}


	protected function increment($num = 1){
		$this->iterator = min($this->total, $this->iterator + $num);
	}


	public function stop(){
		$this->iterator = $this->total;
		parent::stop();
	}


}