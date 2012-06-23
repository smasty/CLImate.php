<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate\Tests;

use CLImate\IO;


/**
 * Tests for CLImate\IO.
 */
class IOTest extends \PHPUnit_Framework_TestCase {


	private $stdIn, $stdOut, $stdErr;


	protected function setUp(){
		$this->stdIn = fopen('php://memory', 'w+');
		$this->stdOut = fopen('php://memory', 'w+');
		$this->stdErr = fopen('php://memory', 'w+');

		IO::$stdIn = $this->stdIn;
		IO::$stdOut = $this->stdOut;
		IO::$stdErr = $this->stdErr;
	}


	protected function tearDown(){
		fclose($this->stdIn);
		fclose($this->stdOut);
		fclose($this->stdErr);

		$this->stdIn = $this->stdOut = $this->stdErr = null;

		IO::$stdIn = STDIN;
		IO::$stdOut = STDOUT;
		IO::$stdErr = STDERR;
	}


	private function streamRead($stream){
		rewind($stream);
		$content = stream_get_contents($stream);
		return $content;
	}


	private function streamWrite($text, $stream){
		fputs($stream, $text);
		rewind($stream);
	}


	public function testRenderPlain(){
		$this->assertEquals('foo', IO::render('foo'));
	}


	public function testRenderNamedArguments(){
		$args = array('foo' => 'lorem', 'bar' => 'ipsum');

		$this->assertEquals("$args[foo] $args[bar]", IO::render('{:foo} {:bar}', $args));
	}


	public function testRenderSprintf(){
		$this->assertEquals('foo bar baz 005', IO::render('foo %s baz %03d', 'bar', 5));
	}


	public function testWrite(){
		IO::write('foo');
		$this->assertEquals('foo', $this->streamRead($this->stdOut));
	}


	public function testLine(){
		IO::line('foo');
		$this->assertEquals("foo\n", $this->streamRead($this->stdOut));
	}


	public function testCr(){
		IO::cr();
		$this->assertEquals("\r", $this->streamRead($this->stdOut));
	}


	public function testError(){
		IO::error('foo');
		$this->assertEquals('foo', $this->streamRead($this->stdErr));
	}


	public function testReadPlain(){
		$this->streamWrite('foo', $this->stdIn);
		$this->assertEquals('foo', IO::read());
	}


	public function testReadFormat(){
		$this->streamWrite('foo', $this->stdIn);
		$this->assertEquals('', IO::read('%d'));
	}


	public function testReadEOF(){
		$this->setExpectedException('CLImate\\InputException', 'Caught ^D during input');
		fwrite($this->stdIn, 'foo');
		IO::read();
	}


	public function testPrompt(){
		$this->streamWrite('foo', $this->stdIn);
		$choice = IO::prompt('Hello', null, ': ');
		$this->assertEquals('Hello: ', $this->streamRead($this->stdOut));
		$this->assertEquals('foo', $choice);
	}


	public function testPromptDefault(){
		$this->streamWrite("\n", $this->stdIn);
		$choice = IO::prompt('Hello', 'default', ': ');
		$this->assertEquals('Hello [default]: ', $this->streamRead($this->stdOut));
		$this->assertEquals('default', $choice);
	}


	public function testPromptEmptyDefault(){
		$this->streamWrite("\n", $this->stdIn);
		$choice = IO::prompt('Hello', '', ': ');
		$this->assertEquals('Hello: ', $this->streamRead($this->stdOut));
		$this->assertEquals('', $choice);
	}


	public function testChoose(){
		$this->streamWrite('y', $this->stdIn);
		$choice = IO::choose('Hello:', 'yn', 'n');
		$this->assertEquals('Hello: [y/N] ', $this->streamRead($this->stdOut));
		$this->assertEquals('y', $choice);
	}


	public function testChooseDefault(){
		$this->streamWrite("\n", $this->stdIn);
		$choice = IO::choose('Hello:', 'yn', 'n');
		$this->assertEquals('Hello: [y/N] ', $this->streamRead($this->stdOut));
		$this->assertEquals('n', $choice);
	}


	public function testMenu(){
		$this->streamWrite(2, $this->stdIn);
		$index = IO::menu($values = array(1, 2, 3), null, 'Choose');
		$this->assertEquals("  1. 1\n  2. 2\n  3. 3\n\nChoose: ",$this->streamRead($this->stdOut));
		$this->assertEquals('2', $values[$index]);
	}


	public function testMenuDefault(){
		$this->markTestSkipped('Not yet implemented');
		$this->streamWrite("\n", $this->stdIn);
		$index = IO::menu($values = array(1, 2, 3), null, 'Choose');
		$this->assertEquals("  1. 1\n  2. 2\n  3. 3\n\nChoose: ",$this->streamRead($this->stdOut));
		$this->assertEquals('2', $values[$index]);
	}


}
