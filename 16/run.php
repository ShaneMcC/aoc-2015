#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$sues = array();
	foreach ($input as $details) {
		preg_match('#^Sue ([0-9]+): (.*)$#SADi', $details, $m);
		preg_match_all('#([a-z]+): ([0-9]+)#i', $m[2], $details);

		$sues[$m[1]] = array();
		for ($i = 0; $i < count($details[0]); $i++) {
			$sues[$m[1]][$details[1][$i]] = $details[2][$i];
		}
	}

	if (count($sues) == 0) {
		echo 'There are no sues.', "\n";
		die();
	}

	$known = array();
	$known['children'] = 3;
	$known['cats'] = 7;
	$known['samoyeds'] = 2;
	$known['pomeranians'] = 3;
	$known['akitas'] = 0;
	$known['vizslas'] = 0;
	$known['goldfish'] = 5;
	$known['trees'] = 3;
	$known['cars'] = 2;
	$known['perfumes'] = 1;

	$possible1 = array();
	$possible2 = array();
	foreach ($sues as $c => $sue) {
		$maybe1 = true;
		$maybe2 = true;
		foreach ($sue as $thing => $count) {
			if (!isset($known[$thing])) { continue; }

			if ($known[$thing] != $count) {
				$maybe1 = false;
			}

			if ($thing == 'cats' || $thing == 'trees') {
				if ($count <= $known[$thing]) {
					$maybe2 = false;
				}
			} else if ($thing == 'pomeranians' || $thing == 'goldfish') {
				if ($count >= $known[$thing]) {
					$maybe2 = false;
				}
			} else if ($known[$thing] != $count) {
				$maybe2 = false;
			}

		}
		if ($maybe1) { $possible1[$c] = $sue; }
		if ($maybe2) { $possible2[$c] = $sue; }
	}


	var_dump($possible1);
	var_dump($possible2);
