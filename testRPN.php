<?php

require_once('index.php');

class RpnTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider expressionProvider
	 */
	public function testRPNcalc($test, $expected) {
	 	$this->assertEquals($expected, RPNcalc($test));
	}

	/**
	 * @dataProvider exprExcProvider
	 * @expectedException Exception 
	 */	
	public function testRPNcalcExceptions($test, $expected) {
	 	$this->expectExceptionMessageRegExp($expected);
	 	RPNcalc($test);
	}

	public function expressionProvider() {
		return [
			["", 0],
			["   \t  \r\n    ", 0],
			["0", 0],
			["  0   ", 0],
			["000", 0],
			["01", 1],
			["-5", -5],
			["2.45687", 2.45687],
			["2 2 +", 4],
			["2 3 -", -1],
			["3 2 *", 6],
			["5 2 /", 2.5],
			["5 2 %", 1],
			["5 2 ^", 25],
			["-25 abs", 25],
			["5 8 3 + *", 55],
			["5.5 7 -", -1.5],
			["20 4.5 1 2 * - / 2 3 4 * + 1.1 * / 10 * 1 -", 4.1948051948052],
			["1 2 / 2 3 + 9 2 - abs 2 ^ 6 7 / - / +", 0.60385756676558],
			["2 15 3 3 + % * 3 2 ^ 5 25 / - / 100 - abs", 99.3181818182],
		];
	}

	public function exprExcProvider() {
		return [
			["9 9", '/RPNcalc: \$expression is malformed/'],
			["bhgh5j45", '/RPNcalc: unexpected token \'bhgh5j45\' at position 0/'],
			["4455 bhgh5j45 +", '/RPNcalc: unexpected token \'bhgh5j45\' at position 1/'],
			["9 9 0 +", '/RPNcalc: \$expression is malformed/'],
			["9 111 0 15 /", '/RPNcalc: \$expression is malformed/'],
			["9 ^", '/RPNcalc: operator \'\^\' at position 1 takes 2 argument\(s\), 1 passed/'],
			["9 3 7 ^ - +", '/RPNcalc: operator \'\+\' at position 5 takes 2 argument\(s\), 1 passed/'],

		];
	}
}
?>
