#!/usr/bin/php
<?php
	$lines = file(!posix_isatty(STDIN) ? 'php://stdin' : dirname(__FILE__) . '/input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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

		echo sprintf('[%dx%dx%d] %d + %d = %d [%d] {%d => %d}', $l, $w, $h, $paper, $slack, ($paper+$slack), $total, $ribbon, $ribbontotal), "\n";
	}

	echo "\n\n";
	echo 'Total: ', $total, "\n";
	echo 'Ribbon: ', $ribbontotal, "\n";
