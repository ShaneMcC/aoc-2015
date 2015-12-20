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
	 * @return Unsorted array of reverse mappings
	 */
	function getReverseMappings($input) {
		$reverse = array();
		foreach ($input as $start => $results) {
			foreach ($results as $res) {
				$reverse[$res] = $start;
			}
		}
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
	 * This moves right->left in the string replacing each match it finds,
	 * counting each replacement it makes.
	 *
	 * This is then repeated multiple times until we can make no more replacements.
	 *
	 * @param $input Desired input.
	 * @param $molecules Molecules that can make up $input
	 * @return Count of replacements needed from 'e', or -1 if we couldn't
	 *         get to e
	 */
	function getFromE($input, $molecules) {
		$reverse = array();
		foreach (getReverseMappings($molecules) as $k => $v) { $reverse[strrev($k)] = strrev($v); }
		$input = strrev($input);

		$count = 0;
		do {
			$start = $input;
			$cb = function($a) use ($reverse, &$count) { $count++; return $reverse[$a[0]]; };
			$input = preg_replace_callback('#'.implode('|', array_keys($reverse)).'#', $cb, $input);
		} while ($start != $input);

		return ($input == 'e') ? $count : -1;
	}

	$replacements = getReplacements($medicine, $molecules);
	echo "Part 1: ", count($replacements), "\n";

	$count = getFromE($medicine, $molecules, 0);
	echo 'Part 2: ', $count, "\n";
