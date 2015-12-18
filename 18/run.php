#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lights = [];
	foreach (getInputLines() as $line) { $lights[] = str_split($line); }

	function getNeighbourCount($lights, $x, $y) {
		$i = 0;
		foreach (range($x - 1, $x + 1) as $x1) {
			foreach (range($y - 1, $y + 1) as $y1) {
				if (isset($lights[$x1][$y1]) && !($x1 == $x && $y1 == $y) && ($lights[$x1][$y1] == '#' || $lights[$x1][$y1] == '@')) {
					$i++;
				}
			}
		}
		return $i;
	}

	function advance(&$lights) {
		$old = $lights;
		for ($x = 0; $x < count($lights); $x++) {
			for ($y = 0; $y < count($lights[$x]); $y++) {
				if ($lights[$x][$y] == '@') { continue; } // Always On.

				$n = getNeighbourCount($old, $x, $y);
				$lights[$x][$y] = ($lights[$x][$y] == '.') ? (($n == 3) ? '#' : '.') : (($n == 2 || $n == 3) ? '#' : '.');
			}
		}
	}

	function countLights($lights) {
		$i = 0;
		foreach ($lights as $x) {
			foreach ($x as $l) {
				if ($l != '.') { $i++; }
			}
		}
		return $i;
	}

	function printLights($lights) {
		foreach ($lights as $l) { echo "\t", implode('', $l), "\n"; }
	}

	function run($lights, $times) {
		if (isDebug()) {
			echo 'Initial State:', "\n";
			printLights($lights);
		}

		for ($i = 0; $i < $times; $i++) {
			advance($lights);
			if (isDebug()) {
				echo 'After step ', $i, ':', "\n";
				printLights($lights);
			}
		}

		return $lights;
	}

	$lights1 = run($lights, (isTest() ? 4 : 100));
	echo "Part 1 Count:", countLights($lights1), "\n";

	// Force all the corners on.
	$lights[0][0] = $lights[0][count($lights[0]) - 1] = $lights[count($lights) - 1][0] = $lights[count($lights) - 1][count($lights[0]) - 1] = '@';

	$lights2 = run($lights, (isTest() ? 5 : 100));
	echo "Part 2 Count:", countLights($lights2), "\n";
