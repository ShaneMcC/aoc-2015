#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	function doLights($lines, $instructions) {
		$debug = isDebug();

		$lights = [];
		for ($x = 0; $x <= 999; $x++) {
			for ($y = 0; $y <= 999; $y++) {
				$lights[$x][$y] = 0;
			}
		}

		foreach ($lines as $line) {
			preg_match('#(turn (?:on|off)|toggle) ([0-9]+),([0-9]+) through ([0-9]+),([0-9]+)#', $line, $matches);
			list($full, $instruction, $x1, $y1, $x2, $y2) = $matches;

			if (isset($instructions[$instruction])) {
				$instruction = $instructions[$instruction];
			} else { continue; }

			for ($x = $x1; $x <= $x2; $x++) {
				for ($y = $y1; $y <= $y2; $y++) {
					if ($debug) { $old = $lights[$x][$y]; }
					$instruction($lights[$x][$y]);

					if ($debug) {
						echo sprintf('"%s" changed "%d,%d" from "%d" to "%d"', $full, $old, $x, $y, $lights[$x][$y]), "\n";
					}
				}
			}
		}

		return $lights;
	}

	$part1 = array('turn on' => function(&$val) { $val = 1; },
		           'turn off' => function(&$val) { $val = 0; },
		           'toggle' => function(&$val) { $val = 1 - $val; },
	              );

	$part2 = array('turn on' => function(&$val) { $val += 1; },
		           'turn off' => function(&$val) { $val = max(0, $val - 1); },
		           'toggle' => function(&$val) { $val += 2; },
	              );

	$lights = doLights($lines, $part1);
	echo 'Lights on: ', multi_array_sum($lights), "\n";

	$lights = doLights($lines, $part2);
	echo 'Lights brightness: ', multi_array_sum($lights), "\n";

