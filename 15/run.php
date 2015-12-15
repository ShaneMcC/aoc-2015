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

	function getPossible4($sum) {
		$possible = array();
		for ($a = 0; $a < $sum; $a++) {
			for ($b = 0; $b < $sum; $b++) {
				for ($c = 0; $c < $sum; $c++) {
					for ($d = 0; $d < $sum; $d++) {
						$p = array($a, $b, $c, $d);
						if (array_sum($p) == $sum) {
							$possible[] = $p;
						}
					}
				}
			}
		}

		return $possible;
	}

	function getPossible2($sum) {
		$possible = array();
		$remaining = $sum;
		for ($a = 0; $a < $sum; $a++) {
			for ($b = 0; $b < $sum; $b++) {
				$p = array($a, $b);
				if (array_sum($p) == $sum) {
					$possible[] = $p;
				}
			}
		}

		return $possible;
	}

	$best = 0;
	$bestQuantities = array();

	$best500 = 0;
	$best500Quantities = array();

	$possible = isTest() ? getPossible2(100) : getPossible4(100);

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
