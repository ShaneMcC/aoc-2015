#!/usr/bin/php
<?php

	$input = trim(file_get_contents('php://STDIN'));

	function findHash($input, $match) {
		echo "\n";
		$i = 0;
		$hash = '';
		while (true) {
			$hash = md5($input . $i);
			printf("\r%.9d (%s)", $i, $hash);
			if (stripos($hash, $match) === 0) { break; }
			$i++;
		}

		echo "\n\n";
		echo 'Input: ', $input, "\n";
		echo 'Match: ', $match, "\n";
		echo 'Number: ', $i, "\n";
		echo 'Final Hash: ', $hash, "\n";
	}

	findHash($input, '00000');
	findHash($input, '000000');
