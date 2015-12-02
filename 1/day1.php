<?php
	$floors = file_get_contents('input.txt');
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
		echo $bit, ' => ', $floor, "\n";
	}

	echo "\n\n";

	echo 'Final Floor: ', $floor, "\n";
	echo 'First basement: ', $char, "\n";

?>
