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
		$distance = 0;
		$action = 'move';
		$count = $reindeer['time'];
		for ($i = 0; $i < $seconds; $i++) {
			if ($action == 'move') {
				$distance += $reindeer['speed'];
			}

			$count -= 1;

			if ($count == 0 & $action == 'move') {
				$action = 'rest';
				$count = $reindeer['rest'];
			} else if ($count == 0 & $action == 'rest') {
				$action = 'move';
				$count = $reindeer['time'];
			}
		}

		return $distance;
	}

	$positions = array();
	foreach ($reindeer as $name => $r) {
		$positions[$name] = calculateDistance($r, 2503);
	}
	asort($positions);
	var_dump($positions);



	$points = array();
	for ($i = 1; $i < 2503; $i++) {
		$positions = array();
		foreach ($reindeer as $name => $r) {
			$positions[$name] = calculateDistance($r, $i);
		}
		$max = max($positions);
		foreach ($positions as $name => $pos) {
			if ($pos == $max) {
				if (!isset($points[$name])) { $points[$name] = 0; }
				$points[$name]++;
			}
		}
	}

	asort($points);
	var_dump($points);

