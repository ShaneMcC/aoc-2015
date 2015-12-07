#!/usr/bin/php
<?php
	// *Whistles*
	ini_set('memory_limit', '-1');

	$lines = file('php://STDIN', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	function doLights($lines, $instructions) {
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

					if (isset($instructions[$instruction])) {
						$lights[$loc] = $instructions[$instruction]($lights[$loc]);
					}
				}
			}
		}

		return $lights;
	}

	$part1 = array('turn on' => function($val) { return 1; },
		           'turn off' => function($val) { return 0; },
		           'toggle' => function($val) { return 1 - $val; },
	              );

	$part2 = array('turn on' => function($val) { return $val += 1; },
		           'turn off' => function($val) { return max(0, $val - 1); },
		           'toggle' => function($val) { return $val += 2; },
	              );

	$lights = doLights($lines, $part1);
	echo 'Lights on: ', array_sum($lights), "\n";

	$lights = doLights($lines, $part2);
	echo 'Lights brightness: ', array_sum($lights), "\n";

