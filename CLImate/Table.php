<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Class for presenting tabular data.
 */
class Table {


	protected $rows = array();

	protected $header = array();

	private $columnLength = array();


	public function __construct($rows = null){
		if($rows !== null)
			$this->setRows($rows);
	}


	public function setRows($rows){
		$rows = $rows instanceof \Traversable ? iterator_to_array($rows) : $rows;
		if(!is_array($rows))
			throw new \InvalidArgumentException('Rows must be an array or a Traversable instance.');

		foreach($rows as $row)
			$this->addRow($row);

		return $this;
	}


	public function addRow($row){
		$row = $row instanceof \Traversable ? iterator_to_array($row) : $row;
		if(!is_array($row))
			throw new \InvalidArgumentException('Row must be an array or a Traversable instance.');

		$this->setColumnLength($row);
		$this->rows[] = $row;

		return $this;
	}


	protected function setColumnLength(array $row){
		foreach($row as $i => $col){
			$this->columnLength[$i] = isset($this->columnLength[$i])
				? max($this->columnLength[$i], strlen($col))
				: strlen($col);
		}
	}


	public function setHeader($header){
		$header = $header instanceof \Traversable ? iterator_to_array($header) : $header;
		if(!is_array($header))
			throw new \InvalidArgumentException('Header must be an array or a Traversable instance.');

		$this->setColumnLength($header);
		$this->header = $header;

		return $this;
	}


	public function render(){
		$this->renderHeader();

		foreach($this->rows as $row)
			$this->renderRow($row);
	}


	protected function renderHeader(){
		$length = 3 * count($this->header) + array_sum($this->columnLength) + 1;
		$this->renderRow($this->header);

		IO::write('|');
		foreach($this->header as $i => $col)
			IO::write('%s|', str_repeat('-', $this->columnLength[$i] + 2));
		IO::line();
	}


	protected function renderRow(array $row){
		IO::write('|');
		foreach($row as $i => $col){
			IO::write(' %s |', str_pad($col, $this->columnLength[$i]));
		}
		IO::line();
	}


}