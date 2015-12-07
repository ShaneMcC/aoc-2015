#!/usr/bin/php
<?php

	function isNicePart1($word) {
		$vowels = array('a', 'e', 'i', 'o', 'u');
		$badStrings = array('ab', 'cd', 'pq', 'xy');

		$duplicate = 0;
		$vowelCount = 0;
		$badCount = 0;
		for ($i = 0; $i < strlen($word); $i++) {
			if (in_array($word[$i], $vowels)) { $vowelCount++; }
			if ($i >=1 && $word[$i-1] == $word[$i]) { $duplicate++; }
			if ($i >=1 && in_array($word[$i-1] . $word[$i], $badStrings)) { $badCount++; }
		}

		return $vowelCount >= 3 && $duplicate >= 1 && $badCount == 0;
	}


	function isNicePart2($word) {
		$splitPairs = 0;
		$pairs = array();
		$pairCount = 0;
		for ($i = 0; $i < strlen($word); $i++) {
			if ($i >= 2 && $word[$i - 2] == $word[$i]) { $splitPairs++; }

			if ($i >= 1) {
				$pair = $word[$i - 1] . $word[$i];
				if (isset($pairs[$pair]) && $pairs[$pair] <= $i-2) {
					$pairCount++;
				} else if (!isset($pairs[$pair])) {
					$pairs[$pair] = $i;
				}
			}
		}

		return $splitPairs >= 1 && $pairCount >= 1;
	}


	$lines = file(!posix_isatty(STDIN) ? 'php://stdin' : dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$count1 = $count2 = 0;
	foreach ($lines as $line) {
		$result1 = isNicePart1($line);
		$result2 = isNicePart2($line);
		echo $line, ' -> ', ($result1 ? 'Nice1' : 'Naughty1'), ' ', ($result2 ? 'Nice2' : 'Naughty2'), "\n";
		if ($result1) { $count1++; }
		if ($result2) { $count2++; }
	}

	echo "\n\n";
	echo 'Nice Words part 1: ', $count1, "\n";
	echo 'Nice Words part 2: ', $count2, "\n";
