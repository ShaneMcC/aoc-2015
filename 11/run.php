#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function isValid($input) {
		if (preg_match('/[iol]/', $input)) { return false; }
		if (!preg_match('/(?:(.)\1).*(?:(.)\2)/', $input, $m)) { return false; }

		$threeInRow = false;
		for ($i = 0; $i < strlen($input); $i++) {
			if ($i >= 2) {
				if (ord($input[$i - 2]) + 1 == ord($input[$i - 1]) && ord($input[$i - 1]) + 1 == ord($input[$i])) {
					return true;
				}
			}
		}

		return false;
	}

	function getNext($input) {
		do {
			$input++;
		} while (!isValid($input));

		return $input;
	}

	echo $input, ': ', ($first = getNext($input)), "\n";
	echo $first, ': ', getNext($first), "\n";
