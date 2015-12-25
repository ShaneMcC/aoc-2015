#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	$row = $col = 0;
	preg_match('#To continue, please consult the code grid in the manual.  Enter the code at row ([0-9]+), column ([0-9]+).#SADi', $input, $m);
	list($all, $row, $col) = $m;

	function getRowCol($row, $col) {
		$code = 20151125;
		$r = $c = 1;
		while (true) {
			if ($r == $row && $c == $col) { return $code; }
			$code = ($code * 252533) % 33554393;

			if ($r == 1) {
				$r = $c + 1;
				$c = 1;
			} else {
				$r--;
				$c++;
			}
		}
	}

	$ans = getRowCol($row, $col);
	echo 'Part 1: ', $ans, "\n";
