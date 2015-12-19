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
	 * Implement Andrew Skalski Mathy solution:
	 *
	 * https://www.reddit.com/r/adventofcode/comments/3xflz8/day_19_solutions/cy4etju
	 *
	 * @param $input Desired input.
	 * @param $molecules Molecules that can make up $input
	 * @return Count of replacements needed from 'e', or -1 if we couldn't
	 *         get to e
	 */
	function askalski($input, $molecules) {
		preg_match_all('/(e|[A-Z][a-z]*)/', $input, $matches);

		$bracket = 0;
		$Y = 0;
		$total = count($matches[1]);

		foreach ($matches[1] as $m) {
			if ($m == 'Rn' || $m == 'Ar') { $bracket++; }
			elseif ($m == 'Y') { $Y++; }
		}

		return $total - $bracket - (2 * $Y) - 1;
	}

	$replacements = getReplacements($medicine, $molecules);
	echo "Part 1: ", count($replacements), "\n";

	$count = askalski($medicine, $molecules);
	echo 'Part 2: ', $count, "\n";
