#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$reindeer = array();
	foreach ($input as $details) {
		preg_match('#([a-z]+) can fly ([0-9]+) km/s for ([0-9]+) seconds, but then must rest for ([0-9]+) seconds.#SADi', $details, $m);
		list($all, $name, $speed, $time, $rest) = $m;

		if (!isset($reindeer[$name])) { $reindeer[$name] = array(); }

		$reindeer[$name] = array('speed' => $speed, 'time' => $time, 'rest' => $rest);
	}

	function calculateDistance($reindeer, $seconds) {
		$full = $reindeer['time'] + $reindeer['rest'];
		$distance = $reindeer['speed'] * $reindeer['time'] * floor($seconds / $full);
		$distance += $reindeer['speed'] * min(($seconds % $full), $reindeer['time']);
		return $distance;
	}

	function getPositions($reindeer, $seconds) {
		$positions = array_map(function($r) use ($seconds) { return calculateDistance($r, $seconds); }, $reindeer);
		asort($positions);
		return $positions;
	}

	function getPoints($reindeer, $seconds) {
		$points = array_map(function($r) { return 0; }, $reindeer);
		for ($i = 1; $i < $seconds; $i++) {
			$positions = getPositions($reindeer, $i);
			$max = max($positions);
			foreach ($positions as $name => $pos) {
				if ($pos == $max) {
					$points[$name]++;
				}
			}
		}
		asort($points);
		return $points;
	}

	$olympicTime = isTest() ? 1000 : 2503;
	$distances = getPositions($reindeer, $olympicTime);
	$points = getPoints($reindeer, $olympicTime);
	$winner1 = key(array_slice($distances, -1, 1));
	$winner2 = key(array_slice($points, -1, 1));

	echo 'After ', $olympicTime, ' seconds the results are: ', "\n";
	echo "\t", $winner1, ' is at ', $distances[$winner1], ' km', "\n";
	echo "\t", $winner2, ' has ', $points[$winner2], ' points', "\n";
