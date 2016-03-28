<?php
$output = [];

exec('php ./vendor/phpunit/phpunit/phpunit testRPN.php', $output);

foreach($output as $line) {
	echo($line.PHP_EOL);
}
?>
