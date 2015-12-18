#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$lights = [];
	$x = 0;
	foreach ($input as $line) {
		for ($y = 0; $y < strlen($line); $y++) {
			$lights[$x][$y] = ($line[$y] == '#');
		}
		$x++;
	}

	function countLights($lights) {
		$i = 0;
		for ($x = 0; $x < count($lights); $x++) {
			for ($y = 0; $y < count($lights[$x]); $y++) {
				if ($lights[$x][$y]) { $i++; }
			}
		}
		return $i;
	}

	function printLights($lights) {
		for ($x = 0; $x < count($lights); $x++) {
			for ($y = 0; $y < count($lights[$x]); $y++) {
				echo $lights[$x][$y] === true ? '#' : '.';
			}
			echo "\n";
		}
	}

	function getNeighbours($lights, $x, $y) {
		$result = array();
		for ($x1 = $x-1; $x1 <= $x+1; $x1++) {
			for ($y1 = $y-1; $y1 <= $y+1; $y1++) {
				if (isset($lights[$x1][$y1]) && !($x1 == $x && $y1 == $y)) {
					$result[] = $lights[$x1][$y1] ? 1 : 0;
				}
			}
		}
		return $result;
	}

	function advance(&$lights, $stuck) {
		$old = $lights;
		for ($x = 0; $x < count($old); $x++) {
			for ($y = 0; $y < count($old[$x]); $y++) {
				$n = array_sum(getNeighbours($old, $x, $y));
				$value = $lights[$x][$y];
				if ($value) {
					$value = ($n == 2 || $n == 3);
				} else {
					$value = ($n == 3);
				}
				$corner = ($x == 0 && $y == 0) || ($x == 0 && $y == count($old[$x])-1) || ($x == count($old)-1 && $y == 0) || ($x == count($old)-1 && $y == count($old[$x])-1);
				if ($stuck && $corner) { $value = true; }
				$lights[$x][$y] = $value;
			}
		}
	}

	function run($lights, $part2) {
		if ($part2) {
			$lights[0][0] = true;
			$lights[0][count($lights[0])-1] = true;
			$lights[count($lights)-1][0] = true;
			$lights[count($lights)-1][count($lights[0])-1] = true;
		}

		$times = isTest() ? ($part2 ? 5 : 4) : 100;
		for ($i = 0; $i < $times; $i++) {
			advance($lights, $part2);
			if (isDebug()) {
				echo $i, "\n";
				printLights($lights);
			}
		}

		return $lights;
	}

	$lights1 = run($lights, false);
	echo "Part 1 Count:", countLights($lights1), "\n";

	$lights2 = run($lights, true);
	echo "Part 2 Count:", countLights($lights2), "\n";
