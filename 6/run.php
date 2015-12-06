#!/usr/bin/php
<?php

	$lines = file('php://STDIN', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	function doPart1($lines) {
		$lights = array();
		foreach ($lines as $line) {
			preg_match('#(turn (?:on|off)|toggle) ([0-9]+),([0-9]+) through ([0-9]+),([0-9]+)#', $line, $matches);
			list($full, $instruction, $x1, $y1, $x2, $y2) = $matches;

			for ($x = $x1; $x <= $x2; $x++) {
				for ($y = $y1; $y <= $y2; $y++) {
					$loc = $x . ',' . $y;
					if ($instruction == 'turn on' || ($instruction == 'toggle' && !isset($lights[$loc]))) {
						$lights[$loc] = true;
					} else if ($instruction == 'turn off' || ($instruction == 'toggle' && isset($lights[$loc]))) {
						unset($lights[$loc]);
					}
				}
			}
		}

		return $lights;
	}

	function doPart2($lines) {
		$lights = array();
		foreach ($lines as $line) {
			preg_match('#(turn (?:on|off)|toggle) ([0-9]+),([0-9]+) through ([0-9]+),([0-9]+)#', $line, $matches);
			list($full, $instruction, $x1, $y1, $x2, $y2) = $matches;

			for ($x = $x1; $x <= $x2; $x++) {
				for ($y = $y1; $y <= $y2; $y++) {
					$loc = $x . ',' . $y;
					if (!isset($lights[$loc])) { $lights[$loc] = 0; }

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

		return $lights;
	}

	$lights = doPart1($lines);
	echo 'Lights on: ', count($lights), "\n";

	$lights = doPart2($lines);
	echo 'Lights brightness: ', array_sum($lights), "\n";

