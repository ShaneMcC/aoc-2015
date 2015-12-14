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

	$olympicDistance = 2503;

	$part1 = getPositions($reindeer, $olympicDistance);
	$winner = array_slice($part1, -1, 1);
	foreach ($winner as $name => $distance) { echo 'Part 1: ', $name, ' is at ', $distance, 'km', "\n"; }

	$part2 = array();
	for ($i = 1; $i < $olympicDistance; $i++) {
		$positions = getPositions($reindeer, $i);
		$max = max($positions);
		foreach ($positions as $name => $pos) {
			if ($pos == $max) {
				if (!isset($part2[$name])) { $part2[$name] = 0; }
				$part2[$name]++;
			}
		}
	}

	asort($part2);
	$winner = array_slice($part2, -1, 1);
	foreach ($winner as $name => $points) { echo 'Part 2: ', $name, ' has ', $points, ' points', "\n"; }
