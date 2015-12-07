#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$lines = getInputLines();
	$total = 0;
	$ribbontotal = 0;
	foreach ($lines as $line) {
		list($l, $w, $h) = explode('x', $line);
		$paper = (2*$l*$w) + (2*$w*$h) + (2*$h*$l);
		$dim = array($l, $w, $h);
		sort($dim);
		$slack = $dim[0] * $dim[1];
		$total += $paper + $slack;

		$ribbon = ($dim[0]*2) + ($dim[1]*2) + ($l*$w*$h);
		$ribbontotal += $ribbon;

		if (isDebug()) {
			echo sprintf('[%dx%dx%d] %d + %d = %d [%d] {%d => %d}', $l, $w, $h, $paper, $slack, ($paper+$slack), $total, $ribbon, $ribbontotal), "\n";
		}
	}

	if (isDebug()) { echo "\n\n"; }
	echo 'Total: ', $total, "\n";
	echo 'Ribbon: ', $ribbontotal, "\n";
