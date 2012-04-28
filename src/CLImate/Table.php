<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Class for presenting tabular data.
 */
class Table {


	/** @var array */
	protected $rows = array();

	/** @var array */
	protected $header = array();

	/** @var array */
	protected $format = array();

	/** @var int */
	protected $align = array();

	/** @var array */
	private $columnLength = array();


	/**
	 * Create a table.
	 *
	 * Three ways of usage:
	 * 1. Headers in the first argument, rows in the second one.
	 * 2. Only one argument - rows with string keys - keys will be used as headers.
	 * 3. No parameters, use `setHeader()` and `setRows`/`addRow()`.
	 * @param \Traversable|array $header
	 * @param \Traversable[]|array[] $rows
	 * @return void
	 */
	public function __construct($header = null, $rows = null){
		if($header){
			if(!$rows){
				$rows = $header instanceof \Traversable ? interator_to_array($header) : (array) $header;
				$keys = array_keys(array_shift($rows));
				$header = array();

				foreach($keys as $h)
					$header[$h] = $h;
			}

			$this->setHeader($header);
			$this->setRows($rows);
		}
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
	 * Sort rows by given column.
	 * @param int|string $column Column index
	 * @param bool $reverse Sort in reverse order
	 * @return Table fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function sort($column, $reverse = false){
		if(!isset($this->header[$column]))
			throw new \InvalidArgumentException("No such column '$column' to sort by.");

		usort($this->rows, function($a, $b) use($column, $reverse){
			if($reverse)
				list($a, $b) = array($b, $a);
			return strcmp($a[$column], $b[$column]);
		});
		return $this;
	}


	/**
	 * Set format of the row values. Either as an array or as a list of arguments.
	 * @param \Traversable|array $format
	 * @return Table fluent interface
	 * @throws \InvalidArgumentException
	 */
	public function setFormat($format = null){
		$this->format = $this->columnLength = array();

		if(func_num_args() >= 1 && !is_array($a = func_get_arg(0)) && !($a instanceof \Traversable))
			$format = func_get_args();

		// Set format
		if($format !== null){
			$format = $format instanceof \Traversable ? iterator_to_array($format) : $format;
			if(!is_array($format))
				throw new \InvalidArgumentException('Format must be an array or a Traversable instance.');
			$this->format = $format;
		}

		return $this;
	}


	/**
	 * Render the table. And optionally set format of the row values.
	 * @param \Traversable|array $format
	 * @return Table fluent interface
	 */
	public function render($format = null){
		if($format !== null)
			call_user_func_array(array($this, 'setFormat'), func_get_args());

		if(!is_array($this->align)){
			$align = array();
			foreach(array_keys($this->header) as $key)
				$align[$key] = $this->align;
			$this->align = $align;
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
	 * Set align of columns.
	 *
	 * Accepts either a single value to set global align,
	 * or an array/list of arguments to set align for particular columns.
	 * @param string[] $align `left` (default), `right` or `center`.
	 * @return Table fluent interface
	 */
	public function align($align){
		if($align instanceof \Traversable)
			$align = iterator_to_array($align);
		elseif(func_num_args() > 1)
			$align = func_get_args();

		if(is_array($align))
			foreach($align as $k => $al)
				$this->align[$k] = $al === 'center'
					? STR_PAD_BOTH : ($al === 'right' ? STR_PAD_LEFT : STR_PAD_RIGHT);
		else
			$this->align = $align === 'center'
				? STR_PAD_BOTH : ($align === 'right' ? STR_PAD_LEFT : STR_PAD_RIGHT);

		return $this;
	}


	/**
	 * Render table header.
	 * @return void
	 */
	protected function renderHeader(){
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
			$align = isset($this->align[$i]) ? $this->align[$i] : STR_PAD_RIGHT;
			IO::write(' %s |', str_pad($str, $this->columnLength[$i], ' ', $align));
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
			$len = IO::strlen(IO::render(isset($this->format[$i]) && $format ? $this->format[$i] : '%s', $col));
			$this->columnLength[$i] = isset($this->columnLength[$i])
				? max($this->columnLength[$i], $len)
				: $len;
		}
	}


}