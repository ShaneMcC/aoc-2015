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


	$quantity = isTest() ? 25 : 150;
	$validSets = array_filter(allSets($containers), function ($a) use ($quantity) { return array_sum($a) == $quantity; });
	echo 'Part 1: ', "\n";
	echo "\t", 'Valid Sets: ', count($validSets), "\n";

	echo "\n";

	usort($validSets, function($a,$b) { return count($a) - count($b); });
	$minCount = count($validSets[0]);
	$minimumSets = array_filter($validSets, function ($a) use ($minCount) { return count($a) == $minCount; });
	echo 'Part 2: ', "\n";
	echo "\t", 'Valid Minimum Sets: ', count($minimumSets), "\n";
	echo "\t", 'Minimum Containers Required: ', $minCount, "\n";
