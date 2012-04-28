<?php
/**
 * Copyright 2012 Martin Srank (http://smasty.net)
 */

namespace CLImate;


/**
 * Colorized output support.
 */
class Color {


	protected static $colors = array(
		'style' => array(
			'bold'    => 1,
			'underline' => 4,
			'blink'     => 5,
			'inverse'   => 6,
			'hidden' => 7
		),
		'color' => array(
			'black'   => 30,
			'red'     => 31,
			'green'   => 32,
			'yellow'  => 33,
			'blue'    => 34,
			'magenta' => 35,
			'cyan'    => 36,
			'white'   => 37
		),
		'background' => array(
			'black'   => 40,
			'red'     => 41,
			'green'   => 42,
			'yellow'  => 43,
			'blue'    => 44,
			'magenta' => 45,
			'cyan'    => 46,
			'white'   => 47
		)
	);


	protected static $codes = array(
		'&r' => array('color' => 'red'),
		'&g' => array('color' => 'green'),
		'&b' => array('color' => 'blue'),
		'&c' => array('color' => 'cyan'),
		'&m' => array('color' => 'magenta'),
		'&y' => array('color' => 'yellow'),
		'&k' => array('color' => 'black'),
		'&w' => array('color' => 'white'),

		'&_r' => array('style' => 'bold', 'color' => 'red'),
		'&_g' => array('style' => 'bold', 'color' => 'green'),
		'&_b' => array('style' => 'bold', 'color' => 'blue'),
		'&_c' => array('style' => 'bold', 'color' => 'cyan'),
		'&_m' => array('style' => 'bold', 'color' => 'magenta'),
		'&_y' => array('style' => 'bold', 'color' => 'yellow'),
		'&_k' => array('style' => 'bold', 'color' => 'black'),
		'&_w' => array('style' => 'bold', 'color' => 'white'),

		'&R' => array('background' => 'red'),
		'&G' => array('background' => 'green'),
		'&B' => array('background' => 'blue'),
		'&C' => array('background' => 'cyan'),
		'&M' => array('background' => 'magenta'),
		'&Y' => array('background' => 'yellow'),
		'&K' => array('background' => 'black'),
		'&W' => array('background' => 'white'),

		'&N' => array('color' => 'reset'),

		'&_' => array('style' => 'bold'),
		'&U' => array('style' => 'underline'),
		'&I' => array('style' => 'inverse'),
		'&H' => array('style' => 'hidden'),
		'&F' => array('style' => 'blink')
	);


	/**
	 * Generates ANSII code for specified color.
	 *
	 * Either a color name, or an array with at least one of the following keys:
	 * `color`, `background`, `style` or `reset`.
	 * @param string|array $color
	 * @return string
	 */
	public static function color($color){
		$color = is_array($color) ? $color : array('color' => $color);
		$color += array('style' => null, 'color' => null, 'background' => null);

		if($color['color'] == 'reset')
			return "\e[0m";

		$codes = array();
		foreach(array('style', 'color', 'background') as $type)
			if(isset(static::$colors[$type][$color[$type]]))
				$codes[] = static::$colors[$type][$color[$type]];

		return "\e[" . implode(';', $codes) . 'm';
	}


	/**
	 * Colorizes given text.
	 * @see Color::$codes
	 * @param string $text
	 * @return string
	 */
	public static function colorize($text){
		$text = preg_replace_callback('~(&\w)~i', function($match){
			return isset(self::$codes[$match[1]]) ? self::color(self::$codes[$match[1]]) : $match[1];
		}, $text);

		return str_replace('&&', '&', $text);
	}


	/**
	 * Removes color codes from text.
	 * @param string $text
	 * @return string
	 */
	public static function removeColors($text){
		$text = preg_replace_callback('~(&\w)~i', function($match){
			return isset(self::$codes[$match[1]]) ? '' : $match[1];
		}, $text);

		return str_replace('&&', '&', $text);
	}


	/**
	 * Returns length of the colored string.
	 * @param string $string
	 * @return int
	 */
	public static function strlen($string){
		return strlen(static::removeColors($string));
	}


}