<?php
error_reporting(E_ALL);

/**
 * RPNcalc
 * Возвращает результат вычисления выражения, записанного в обратной польской нотации
 *
 * @param string $expression Строка с выражением
 * @throws Exceprion В случае, если переданное выражение сформировано с ошибкой или не является строкой
 * @return float
 */ 
function RPNcalc($expression) {
	//Стэк операндов
	$opStack = new SplStack();
	// Массив всех операторов и функций, можно добавить произвольную функцию с любым количеством аргументов
	// Очевидно, этот массив лучше вынести из функции, например, в статическое поле класса (если применяется ООП)
	$operators = [
		'+' 		=>			function ($a, $b) {return $a+$b;},
		'-' 		=>			function ($a, $b) {return $a-$b;},
		'*' 		=>			function ($a, $b) {return $a*$b;},
		'/' 		=>			function ($a, $b) {return $a/$b;}, //TBD: Можно сделать здесь особую обработку деления на ноль
		'%' 		=>			function ($a, $b) {return $a%$b;},
		'^' 		=>			function ($a, $b) {return pow($a, $b);},
		'abs' 	=>			function ($a) {return abs($a);},
	];

	if (!is_string($expression)) { // это если нет declare(strict_types=1) и подсказок типов
		throw new Exception(__FUNCTION__.': $expression must be a string'); // а можно просто вернуть 0
	}
	if (!strlen($expression)) {
		return 0;
	}

	$expression = explode(' ', trim(preg_replace('/\s+/', ' ', $expression)));

	foreach($expression as $k => $v) {
		if (!(float)$v && $v != '0') {
			if (!isset($operators[$v]) || empty($opStack)) {
				throw new Exception(__FUNCTION__.": unexpected token '{$v}' at position {$k}");
			}

			$fRef = new ReflectionFunction($operators[$v]);
			$argCount = $fRef->getNumberOfParameters();
			$opArgs = Array($argCount);

			if (count($opStack) < $argCount) {
				throw new Exception(__FUNCTION__.": operator '{$v}' at position {$k} takes {$argCount} argument(s), ".count($opStack)." passed");
			}

			for ($i = 0; $i <= $argCount-1; $i++) {
				$opArgs[$argCount-$i-1] = $opStack->pop();
			}
			
			$opStack->push($fRef->invokeArgs($opArgs));	
		} else {
			$opStack->push((float)$v);
		}
	}

	if (count($opStack) != 1) {
		throw new Exception(__FUNCTION__.': $expression is malformed');
	}

	return $opStack->pop();
}

echo(RPNcalc("5 8 3 + *")."<br />"); // 55
echo(RPNcalc("5.5 7 -")."<br />"); // -1.5
echo(RPNcalc("1 2 / 2 3 + 9 2 - abs 2 ^ 6 7 / - / +")."<br />"); // 0.60385756676558
echo(RPNcalc("20 4.5 1 2 * - / 2 3 4 * + 1.1 * / 10 * 1 -")); // 4.(194805)
?>