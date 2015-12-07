#!/usr/bin/php
<?php
	// *Whistles*
	ini_set('memory_limit', '-1');

	$lines = file('php://STDIN', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	function doLights($lines, $part1 = true) {
		$startMemory = memory_get_usage();

		$lights = [];
		for ($x = 0; $x <= 999; $x++) {
			for ($y = 0; $y <= 999; $y++) {
				$lights[$x . ',' . $y] = 0;
			}
		}

		foreach ($lines as $line) {
			preg_match('#(turn (?:on|off)|toggle) ([0-9]+),([0-9]+) through ([0-9]+),([0-9]+)#', $line, $matches);
			list($full, $instruction, $x1, $y1, $x2, $y2) = $matches;

			for ($x = $x1; $x <= $x2; $x++) {
				for ($y = $y1; $y <= $y2; $y++) {
					$loc = $x . ',' . $y;

					if ($part1) {
						if ($instruction == 'turn on' || ($instruction == 'toggle' && $lights[$loc] == 0)) {
							$lights[$loc] = 1;
						} else if ($instruction == 'turn off' || ($instruction == 'toggle' && $lights[$loc] == 1)) {
							$lights[$loc] = 0;
						}
					} else {
						if ($instruction == 'turn on') {
							$lights[$loc]++;
						} else if ($instruction == 'turn off' && $lights[$loc] > 0) {
							$lights[$loc]--;
						} else if ($instruction == 'toggle') {
							$lights[$loc] += 2;
						}
					}
				}
			}
		}

		return $lights;
	}

	$lights = doLights($lines, true);
	echo 'Lights on: ', array_sum($lights), "\n";

	$lights = doLights($lines, false);
	echo 'Lights brightness: ', array_sum($lights), "\n";

