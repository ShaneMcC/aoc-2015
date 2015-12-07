#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$floors = getInputLine();

	$char = 0;
	$floor = 0;
	$basement = false;
	foreach (str_split($floors) as $bit) {
		if (!$basement) { $char++; }
		if ($bit == ')') {
			$floor--;
		} else if ($bit == '(') {
			$floor++;
		}
		if ($floor <= -1) { $basement = true; }
		if (isDebug()) {
			echo $bit, ' => ', $floor, "\n";
		}
	}

	if (isDebug()) { echo "\n\n"; }

	echo 'Final Floor: ', $floor, "\n";
	echo 'First basement: ', $char, "\n";

?>
