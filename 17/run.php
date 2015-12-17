#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$containers = getInputLines();

	$quantity = isTest() ? 25 : 150;
	$validSets = array_filter(getAllSets($containers), function ($a) use ($quantity) { return array_sum($a) == $quantity; });
	echo 'Part 1: ', "\n";
	echo "\t", 'Valid Sets: ', count($validSets), "\n";

	echo "\n";

	usort($validSets, function($a,$b) { return count($a) - count($b); });
	$minCount = count($validSets[0]);
	$minimumSets = array_filter($validSets, function ($a) use ($minCount) { return count($a) == $minCount; });
	echo 'Part 2: ', "\n";
	echo "\t", 'Valid Minimum Sets: ', count($minimumSets), "\n";
	echo "\t", 'Minimum Containers Required: ', $minCount, "\n";
