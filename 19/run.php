#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$medicine = '';
	$molecules = array();
	foreach ($input as $details) {
		if (preg_match('#(.*) => (.*)#SADi', $details, $m)) {
			list($all, $start, $replacement) = $m;
			if (!isset($molecules[$start])) { $molecules[$start] = array(); }
			$molecules[$start][] = $replacement;
		} else if (!empty($details)) {
			$medicine = $details;
		}
	}

	/**
	 * Reverse the molecule mappings.
	 *
	 * @param $input Input array of molecules.
	 * @return Array of reverse mappings, sorted longest->shortest
	 */
	function getReverseMappings($input) {
		$reverse = array();
		foreach ($input as $start => $results) {
			foreach ($results as $res) {
				$reverse[$res] = $start;
			}
		}
		uksort($reverse, function($a,$b) { return strlen($b) - strlen($a); });
		return $reverse;
	}

	/**
	 * For a given input, get an array of all the replacements that it can
	 * generate.
	 *
	 * @param $in Input molecule as a string.
	 * @return Array of possible outcomes.
	 */
	function getReplacements($in, $molecules) {
		$replacements = array();

		preg_match_all('/(e|[A-Z][a-z]*)/', $in, $match);

		for ($i = 0; $i < count($match[1]); $i++) {
			$r = $match[1];
			$m = $r[$i];
			if (isset($molecules[$m])) {
				foreach ($molecules[$m] as $mole) {
					$r[$i] = $mole;
					$replacements[] = implode('', $r);
				}
			}
		}

		return array_unique($replacements);
	}

	/**
	 * Take a given input, and find how many replacements are needed to get
	 * to there from a start of 'e'.
	 *
	 * This loops repeatedly, finding the single LONGEST replacement it can
	 * make and making it each time, until such time as we can make no more
	 * replacements. Hopefully by then, we are at 'e'.
	 *
	 * @param $input Desired input.
	 * @param $molecules Molecules that can make up $input
	 * @return Count of replacements needed from 'e', or -1 if we couldn't
	 *         get to e
	 */
	function getFromE($input, $molecules) {
		$reverse = getReverseMappings($molecules);
		$result = 0;
		do {
			$input = isset($out) ? $out : $input;
			foreach ($reverse as $k => $v) {
				if (strpos($input, $k) !== false) {
					if (isDebug()) { echo $k, " => ", $v, "\n"; }
					$out = preg_replace('/'.$k.'/', $v, $input, 1);
					$result++;
					break;
				}
			}
			if (isDebug()) { echo $input, "\n", $result, "\n"; }
		} while (isset($out) && $input != $out);
		return ($input == 'e') ? $result : -1;
	}

	$replacements = getReplacements($medicine, $molecules);
	echo "Part 1: ", count($replacements), "\n";

	$count = getFromE($medicine, $molecules);
	echo 'Part 2: ', $count, "\n";
