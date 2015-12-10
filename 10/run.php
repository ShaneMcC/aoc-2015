#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function lookAndSay($input, $count) {
		$original = $input;

		echo "\n\n";
		echo 'Describing: "', $original, '" ', $count, ' times.', "\n";
		for ($i = 0; $i < $count; $i++) {
			$input = preg_replace_callback('/((.)\2*)/', function($matches) { return strlen($matches[1]) . $matches[2];}, $input);
			if (isDebug()) { echo $input, "\n"; }
		}
		echo 'Length: ', strlen($input), "\n";
	}

	if (isTest()) {
		lookAndSay($input, 5);
	} else {
		lookAndSay($input, 40);
		lookAndSay($input, 50);
	}
