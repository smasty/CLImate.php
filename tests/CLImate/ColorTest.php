<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Tests;

use CLImate\Color;


/**
 * Tests for CLImate\Color.
 */
class ColorTest extends \PHPUnit_Framework_TestCase {


	public function colorCodesDataProvider(){
		return array(
			array('red', "\e[31m"),
			array('reset', "\e[0m"),
			array(array('color' => 'red'), "\e[31m"),
			array(array('background' => 'red'), "\e[41m"),
			array(array('style' => 'bold'), "\e[1m"),
			array(array('color' => 'red', 'background' => 'red'), "\e[31;41m"),
			array(array('color' => 'red', 'background' => 'red', 'style' => 'bold'), "\e[1;31;41m"),
		);
	}


	/**
	 * @dataProvider colorCodesDataProvider
	 */
	public function testColorCode($color, $ansiCode){
		$this->assertEquals($ansiCode, Color::colorCode($color));
	}


	public function testColorize(){
		$colored = "&rRed text &wWhite text. &RRed background. &_rRed bold.";
		$ansi = "\e[31mRed text \e[37mWhite text. \e[41mRed background. \e[1;31mRed bold.";
		$this->assertEquals($ansi, Color::colorize($colored));
	}


	public function testRemoveColors(){
		$colored = "&rRed text &wWhite text. &RRed background. &_rRed bold.";
		$plain = "Red text White text. Red background. Red bold.";
		$this->assertEquals($plain, Color::removeColors($colored));
	}


	public function testStrlen(){
		$colored = "&rRed text &wWhite text. &RRed background. &_rRed bold.";
		$plain = "Red text White text. Red background. Red bold.";
		$this->assertEquals(strlen($plain), Color::strlen($colored));
	}


}