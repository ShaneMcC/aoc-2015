#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$substances = array();
	foreach ($input as $details) {
		preg_match('#([a-z]+): capacity ([-0-9]+), durability ([-0-9]+), flavor ([-0-9]+), texture ([-0-9]+), calories ([-0-9]+)#SADi', $details, $m);
		list($all, $name, $capacity, $durability, $flavor, $texture, $calories) = $m;

		if (!isset($substance[$name])) { $substances[$name] = array(); }

		$substances[$name] = array('capacity' => $capacity, 'durability' => $durability, 'flavor' => $flavor, 'texture' => $texture, 'calories' => $calories);
	}

	function calculateScore($substances, $quantities) {
		$score = array('capacity' => 0, 'durability' => 0, 'flavor' => 0, 'texture' => 0, 'calories' => 0);

		foreach ($substances as $name => $values) {
			foreach ($values as $type => $value) {
				$score[$type] += $quantities[$name] * $value;
			}
		}

		$result = 1;
		foreach ($score as $type => $value) {
			if ($type == 'calories') { continue; }
			$result *= max($value, 0);
		}

		return array($result, $score['calories']);
	}

	function getPossible($count, $sum) {
		if ($count == 0) { return array(); }

		$result = array();
		for ($i = 0; $i <= $sum; $i++) {
			$next = getPossible($count - 1, $sum - $i);
			if (count($next) == 0) {
				$add = array($i);
				if (array_sum($add) == $sum) { $result[] = $add; }
			} else {
				for ($j = 0; $j < count($next); $j++) {
					array_unshift($next[$j], $i);
					$add = $next[$j];
					if (array_sum($add) == $sum) { $result[] = $add; }
				}
			}
		}

		return $result;
	}

	$teaspoons = 100;

	$best = 0;
	$bestQuantities = array();
	$best500 = 0;
	$best500Quantities = array();

	$possible = getPossible(count($substances), $teaspoons);

	foreach ($possible as $p) {
		$quantities = array_combine(array_keys($substances), $p);
		list($score, $calories) = calculateScore($substances, $quantities);
		if ($score > $best) {
			$best = $score;
			$bestQuantities = $quantities;
		}

		if ($score > $best500 && $calories == 500) {
			$best500 = $score;
			$best500Quantities = $quantities;
		}
	}

	echo 'Part1', "\n";
	var_dump($bestQuantities);
	echo $best, "\n";

	echo "\n\n";
	echo 'Part2', "\n";
	var_dump($best500Quantities);
	echo $best500, "\n";
