#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$sues = array();
	foreach ($input as $details) {
		if (preg_match('#^Sue ([0-9]+): (.*)$#SADi', $details, $m)) {
			if (preg_match_all('#([a-z]+): ([0-9]+)#i', $m[2], $values)) {
				$sues[$m[1]] = array();
				for ($i = 0; $i < count($values[0]); $i++) {
					$sues[$m[1]][$values[1][$i]] = $values[2][$i];
				}
			}
		}
	}

	if (count($sues) == 0) { die('There are no sues.'."\n"); }

	$known = array();
	$known['children'] = array('=', 3);
	$known['cats'] = array('>', 7);
	$known['samoyeds'] = array('=', 2);
	$known['pomeranians'] = array('<', 3);
	$known['akitas'] = array('=', 0);
	$known['vizslas'] = array('=', 0);
	$known['goldfish'] = array('<', 5);
	$known['trees'] = array('>', 3);
	$known['cars'] = array('=', 2);
	$known['perfumes'] = array('=', 1);

	function getSue($sues, $known, $alwaysEquals = true) {
		foreach ($sues as $c => $sue) {
			foreach ($sue as $thing => $sueCount) {
				if (!isset($known[$thing])) { continue; }
				list($expr, $count) = $known[$thing];
				if ($alwaysEquals) { $expr = '='; }

				if (($expr == '<' && $count <= $sueCount) || ($expr == '>' && $count >= $sueCount) || ($expr == '=' && $count != $sueCount)) {
					continue 2;
				}
			}
			return array($c, $sue);
		}

		return array(0, array());
	}

	list($number, $detail) = getSue($sues, $known, true);
	echo 'Part 1 Sue: ', $number, "\n";

	list($number, $detail) = getSue($sues, $known, false);
	echo 'Part 2 Sue: ', $number, "\n";
