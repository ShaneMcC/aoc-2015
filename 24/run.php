#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$boxes = getInputLines();

	function getMinimumBoxProduct($boxes, $bins) {
		$max = array_sum($boxes) / $bins;
		$smallest = PHP_INT_MAX;

		$found = false;
		for ($i = 0; $i < count($boxes); $i++) {
			foreach (getAllSets($boxes, $i) as $set) {
				if (array_sum($set) == $max) {
					$prod = array_product($set);
					if ($prod < $smallest) { $smallest = $prod; }
					$found = true;
				}
			}
			if ($found) { break; }
		}

		return $smallest;
	}

	echo 'Part 1: ', getMinimumBoxProduct($boxes, 3), "\n";
	echo 'Part 2: ', getMinimumBoxProduct($boxes, 4), "\n";
