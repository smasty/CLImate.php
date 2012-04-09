<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Class for presenting tabular data.
 * @todo Refactodtor render() - resetDisplay(), setFormat(), set STR_PAD.
 * @todo sort()
 */
class Table {


	/** @var array */
	protected $rows = array();

	/** @var array */
	protected $header = array();

	/** @var array */
	protected $format = array();

	/** @var array */
	private $columnLength = array();


	/**
	 * Create a table.
	 * @param \Traversable[]|array[] $rows
	 */
	public function __construct($rows = null){
		if($rows !== null)
			$this->setRows($rows);
	}


	/**
	 * Set table rows.
	 * @param \Traversable[]|array[] $rows
	 * @return Table fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setRows($rows){
		$rows = $rows instanceof \Traversable ? iterator_to_array($rows) : $rows;
		if(!is_array($rows))
			throw new \InvalidArgumentException('Rows must be an array or a Traversable instance.');

		foreach($rows as $row)
			$this->addRow($row);

		return $this;
	}


	/**
	 * Add a new row.
	 * @param array|\Traversable $row
	 * @return Table fluent interfaces
	 * @throws \InvalidArgumentException
	 */
	public function addRow($row){
		$row = $row instanceof \Traversable ? iterator_to_array($row) : $row;
		if(!is_array($row))
			throw new \InvalidArgumentException('Row must be an array or a Traversable instance.');

		$this->rows[] = $row;
		return $this;
	}


	/**
	 * Set table header.
	 * @param \Traversable|array $header
	 * @return Table fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setHeader($header){
		$header = $header instanceof \Traversable ? iterator_to_array($header) : $header;
		if(!is_array($header))
			throw new \InvalidArgumentException('Header must be an array or a Traversable instance.');

		$this->header = $header;

		return $this;
	}


	/**
	 * Render the table.
	 * @return Table fluent interface
	 */
	public function render($format = null){
		// Reset format and column length
		$this->format = $this->columnLength = array();

		// Format as array or list of arguments
		if(func_num_args() >= 1 && !is_array($a = func_get_arg(0)) && !($a instanceof \Traversable))
			$format = func_get_args();

		// Set format
		if($format !== null){
			$format = $format instanceof \Traversable ? iterator_to_array($format) : $format;
			if(!is_array($format))
				throw new \InvalidArgumentException('Format must be an array or a Traversable instance.');
			$this->format = $format;
		}

		// Compute length of columns
		$this->setColumnLength($this->header, false);
		foreach($this->rows as $row)
			$this->setColumnLength($row);

		// Render
		if($this->header)
			$this->renderHeader();

		foreach($this->rows as $row)
			$this->renderRow($row);

		return $this;
	}


	/**
	 * Render table header.
	 * @return void
	 */
	protected function renderHeader(){
		$length = 3 * count($this->header) + array_sum($this->columnLength) + 1;
		$this->renderRow($this->header, false);

		IO::write('|');
		foreach($this->header as $i => $col)
			IO::write('%s|', str_repeat('-', $this->columnLength[$i] + 2));
		IO::line();
	}


	/**
	 * Render table row.
	 * @param array $row
	 * @param bool $format Format row values?
	 * @return void
	 */
	protected function renderRow(array $row, $format = true){
		IO::write('|');
		foreach($row as $i => $col){
			$str = IO::render(isset($this->format[$i]) && $format ? $this->format[$i] : '%s', $col);
			IO::write(' %s |', str_pad($str, $this->columnLength[$i]));
		}
		IO::line();
	}


	/**
	 * Set lengths for columns.
	 * @param array $row
	 * @param bool $format Count format as well?
	 * @return void
	 */
	private function setColumnLength(array $row, $format = true){
		foreach($row as $i => $col){
			$len = strlen(IO::render(isset($this->format[$i]) && $format ? $this->format[$i] : '%s', $col));
			$this->columnLength[$i] = isset($this->columnLength[$i])
				? max($this->columnLength[$i], $len)
				: $len;
		}
	}


}