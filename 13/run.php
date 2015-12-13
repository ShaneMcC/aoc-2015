#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$people = array();

	foreach ($input as $details) {
		preg_match('#(.*) would (gain|lose) ([0-9]+) happiness units by sitting next to (.*).#SAD', $details, $m);
		list($all, $who, $direction, $units, $person) = $m;

		if (!isset($people[$who])) { $people[$who] = array(); }

		$people[$who][$person] = ($direction == 'lose') ? 0 - $units : $units;
	}

	function calculateHappiness($people, $order) {
		$total = 0;
		for ($i = 0; $i < count($order); $i++) {
			$last = ($i == 0) ? count($order) - 1 : $i - 1;
			$next = ($i + 1) % count($order);

			$total += $people[$order[$i]][$order[$next]];
			$total += $people[$order[$i]][$order[$last]];
		}

		return $total;
	}

	$perms = getPermutations(array_keys($people));

	$bestPerm = array();
	$bestHappiness = 0;
	foreach ($perms as $p) {
		$happiness = calculateHappiness($people, $p);

		if ($happiness > $bestHappiness) {
			$bestHappiness = $happiness;
			$bestPerm = $p;
		}
	}

	echo 'Part 1: ', $bestHappiness, "\n";


	$people['You'] = array();
	foreach (array_keys($people) as $p) {
		$people['You'][$p] = 0;
		$people[$p]['You'] = 0;
	}

	$perms = getPermutations(array_keys($people));

	$bestPerm = array();
	$bestHappiness = 0;
	foreach ($perms as $p) {
		$happiness = calculateHappiness($people, $p);

		if ($happiness > $bestHappiness) {
			$bestHappiness = $happiness;
			$bestPerm = $p;
		}
	}

	echo 'Part 3: ', $bestHappiness, "\n";
