#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputContent();

	$total = 0;
	preg_match_all('/-?[0-9]+/', $input, $m);
	foreach ($m[0] as $i) { $total += (int)$i; }

	echo 'Part 1 Total Numbers: ', $total, "\n";

	function sumJson($json, $exclude = array()) {
		$total = 0;
		foreach ($json as $bit) {
			if (is_array($bit)) {
				$total += sumJson($bit, $exclude);
			} else if (is_object($bit)) {
				$hasBad = false;
				foreach ($bit as $val) {
					if (is_string($val) && in_array((string)$val, $exclude)) {
						$hasBad = true;
						break;
					}
				}
				if (!$hasBad) { $total += sumJson($bit, $exclude); }
			} else if (is_numeric($bit)) {
				$total += $bit;
			}
		}

		return $total;
	}

	// Now actually do something more clever for part 2.
	$json = json_decode($input);
	echo 'Part 2 Total Count: ', sumJson($json, array("red")), "\n";
