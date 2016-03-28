<?php
$output = [];

exec('php ./vendor/phpunit/phpunit/phpunit testRPN.php', $output);

foreach($output as $k=>$v) {
	echo($v.PHP_EOL);
}
?>
