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

	/**
	 * Get all the possible combinations of $count numbers that add up to $sum
	 *
	 * @param $count Amount of values required in sum.
	 * @param $sum Sum we need to add up to
	 * @return Array of possible combinations.
	 */
	function getPossible($count, $sum) {
	    if ($count == 1) {
	        yield array($sum);
	    } else {
	        foreach (range(0, $sum) as $i) {
	            foreach (getPossible($count - 1, $sum - $i) as $j) {
	                yield array_merge(array($i), $j);
	            }
	        }
		}
	}

	function getBest($substances, $teaspoons, $calorieRequirement = 0) {
		$best = 0;
		$bestQuantities = array();

		foreach (getPossible(count($substances), $teaspoons) as $p) {
			$quantities = array_combine(array_keys($substances), $p);
			list($score, $calories) = calculateScore($substances, $quantities);
			if ($score > $best && ($calorieRequirement == 0 || $calories == $calorieRequirement)) {
				$best = $score;
				$bestQuantities = $quantities;
			}
		}

		return array($best, $bestQuantities);
	}


	$teaspoons = 100;
	$calories = 500;

	echo 'Part1', "\n";
	list($score, $quantities) = getBest($substances, $teaspoons);
	print_r($quantities);
	echo 'Score: ', $score, "\n";

	echo "\n\n";
	echo 'Part2', "\n";
	list($score, $quantities) = getBest($substances, $teaspoons, $calories);
	print_r($quantities);
	echo 'Score: ', $score, "\n";
