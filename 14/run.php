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
		$positions = array();
		foreach ($reindeer as $name => $r) {
			$positions[$name] = calculateDistance($r, $seconds);
		}
		asort($positions);
		return $positions;
	}

	function getPoints($reindeer, $seconds) {
		$points = array();
		for ($i = 1; $i < $seconds; $i++) {
			$positions = getPositions($reindeer, $i);
			$max = max($positions);
			foreach ($positions as $name => $pos) {
				if ($pos == $max) {
					if (!isset($points[$name])) { $points[$name] = 0; }
					$points[$name]++;
				}
			}
		}
		asort($points);
		return $points;
	}

	$olympicTime = 2503;

	echo 'After ', $olympicTime, ' seconds the results are: ', "\n";
	$part1 = getPositions($reindeer, $olympicTime);
	$winner = array_slice($part1, -1, 1);
	foreach ($winner as $name => $distance) { echo "\t", $name, ' is at ', $distance, ' km', "\n"; }

	$part2 = getPoints($reindeer, $olympicTime);
	$winner = array_slice($part2, -1, 1);
	foreach ($winner as $name => $points) { echo "\t", $name, ' has ', $points, ' points', "\n"; }
