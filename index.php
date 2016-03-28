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
	];

	if (!is_string($expression)) { // это если нет declare(strict_types=1) и подсказок типов
		throw new Exception(__FUNCTION__.': $expression must be a string'); // а можно просто вернуть 0
	}

	$expression = trim(preg_replace('/\s+/', ' ', $expression));

	if (!strlen($expression)) {
		return 0;
	}

	$expression = explode(' ', $expression);

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
?>
