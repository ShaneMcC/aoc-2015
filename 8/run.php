#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	$total = 0;
	$chars = 0;
	$enc = 0;

	foreach ($lines as $line) {
		echo 'In: ', $line, "\n";
		$len = strlen($line);

		preg_match('#"(.*)"#SAD', $line, $parsed);
		$parsed = $parsed[1];
		$parsed = preg_replace('#\\\(["\\\])#', '\1', $parsed);
		$parsed = preg_replace_callback('#\\\x([A-F0-9]{2})#i', function($matches) { return chr(hexdec($matches[1])); }, $parsed);

		echo "\t", 'Parsed: ', $parsed, "\n";
		$plen = strlen($parsed);

		$encoded = $line;
		$encoded = preg_replace('#["\\\]#', '\\\1', $encoded);
		$encoded = '"' . $encoded . '"';
		echo "\t", 'Encoded: ', $encoded, "\n";
		$elen = strlen($encoded);


		$total += $len;
		$chars += $plen;
		$enc += $elen;
	}

	echo "\n\n";
	echo 'Total Length: ', $total, "\n";
	echo 'Total Memory: ', $chars, "\n";
	echo 'Total Encoded: ', $enc, "\n";
	echo "\n";
	echo 'Memory Difference: ', ($total - $chars), "\n";
	echo 'Encoded Difference: ', ($enc - $total), "\n";
