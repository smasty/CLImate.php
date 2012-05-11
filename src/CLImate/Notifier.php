<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Abstract base class for all notificators - progress bars, spinners, etc.
 */
abstract class Notifier {

	/** @var int */
	protected $interval;

	/** @var int */
	protected $iterator = 0;

	/** @var int */
	protected $start;

	/** @var int */
	protected $timer;

	/** @var string */
	protected $message;

	/** @var bool */
	protected $last = false;


	/**
	 * Create the notifier.
	 * @param string $message Message to display
	 * @param int $interval Refresh interval
	 */
	public function __construct($message, $interval = 100){
		$this->message = (string) $message;
		$this->interval = (int) $interval;

		if($interval <= 0)
			throw new \InvalidArgumentException('Interval must be positive.');
	}


	/**
	 * Display the notification.
	 * @param $return Return the notification instead of printing it.
	 * @return null|string
	 */
	abstract public function display($return = false);


	/**
	 * Number of ticks performed.
	 * @return int
	 */
	public function count(){
		return $this->iterator;
	}


	/**
	 * Time elapsed since the first tick (in seconds).
	 * @return int
	 */
	public function elapsed(){
		if(!$this->start)
			return 0;

		return time() - $this->start;
	}


	/**
	 * Momentary average speed of ticks per second.
	 * @todo Better implementation
	 * @return int
	 */
	public function speed(){
		return $this->elapsed() ? (int) ($this->count() / $this->elapsed()) : 0;
	}


	/**
	 * Determine whether the notifier should be refreshed.
	 * @return bool
	 */
	public function shouldRefresh(){
		$now = (int) (microtime(true) * 1e3);
		if(!$this->timer){
			$this->start = time();
			$this->timer = $now;
			return true;
		}

		if($now - $this->timer > $this->interval){
			$this->timer = $now;
			return true;
		}

		return false;
	}


	/**
	 * Increase the number of performed ticks by given value.
	 * @param int $num
	 */
	protected function increment($num = 1){
		$this->iterator += $num;
	}


	/**
	 * Perform a tick.
	 * @param int $interval Number of ticks to perform
	 */
	public function tick($ticks = 1){
		$this->increment($ticks);

		if($this->shouldRefresh()){
			$this->display();
		}
	}


	/**
	 * Stop the notifier. Should be called after the last tick.
	 */
	public function stop(){
		$this->last = true;
		$this->display();
		IO::line();
	}


	/**
	 * Return time in MM:SS format.
	 * @param int $time
	 * @return string
	 */
	public function formatTime($time){
		return sprintf('%02d:%02d', $time / 60, $time % 60);
	}


}