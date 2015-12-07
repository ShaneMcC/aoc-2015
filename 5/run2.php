#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	function isNicePart1($word) {
		return preg_match('/(.*[aeiou]){3}/', $word) && !preg_match('/(ab|cd|pq|xy)/', $word) && preg_match('/.*(.)\1.*/', $word);
	}

	function isNicePart2($word) {
		return preg_match('/(..).*\1/', $word, $foo) && preg_match('/(.).\1/', $word);
	}

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
