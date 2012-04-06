<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Abstract base class for all notificators - progress bars, spinners, etc.
 */
abstract class Notificator {

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
	 * Create the notificator.
	 * @param int $interval
	 * @return void
	 */
	public function __construct($message, $interval = 100){
		$this->message = (string) $message;
		$this->interval = abs((int) $interval);
	}


	/**
	 * Display the notification.
	 * @return void
	 */
	abstract public function display();


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
	 * Perform a tick.
	 * @param int $interval Number of ticks to perform
	 * @return void
	 */
	public function tick($interval = 1){
		$this->iterator += $interval;

		if($this->shouldRefresh()){
			IO::cr();
			$this->display();
		}
	}


	/**
	 * Stop the notifier. Should be called after the last tick.
	 * @return void
	 */
	public function stop(){
		IO::cr();
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