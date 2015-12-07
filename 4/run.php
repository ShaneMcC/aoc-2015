#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function findHash($input, $match) {
		$i = 0;
		$hash = '';

		echo 'Input: ', $input, "\n";
		echo 'Match: ', $match, "\n";

		while (true) {
			$hash = md5($input . $i);
			printf("\rNumber: %d (%s)", $i, $hash);
			if (stripos($hash, $match) === 0) { break; }
			$i++;
		}

		echo "\n";
	}

	findHash($input, '00000');
	echo "\n\n";
	findHash($input, '000000');
