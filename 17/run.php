#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$containers = array();
	foreach ($input as $details) {
		preg_match('#([0-9]+)#SADi', $details, $m);
		list($all, $size) = $m;

		$containers[] = $size;
	}

	function allSets($array) {
		$result = array(array());

		foreach ($array as $element) {
			foreach ($result as $combination) {
				$result[] = array_merge(array($element), $combination);
			}
		}

		return $result;
	}

$sets = allSets($containers);

$number = count($containers);
$count = 0;
foreach ($sets as $s) {
	if (array_sum($s) == 150) {
		$count++;
	}
}

echo "Part 1: ", $count, "\n";

$number = count($containers);
$count = 0;
foreach ($sets as $s) {
	if (array_sum($s) == 150) {
		if (count($s) < $number) {
			$count = 0;
			$number = count($s);
		}
		if (count($s) == $number) {
			$count++;
		}
	}
}

echo "Part 2: ", $count, "\n";
