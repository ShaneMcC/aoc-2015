#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function lookAndSay($input, $count) {
		$original = $input;

		echo "\n\n";
		echo 'Describing: "', $original, '" ', $count, ' times.', "\n";
		for ($i = 0; $i < $count; $i++) {
			$num = 0;
			$last = null;
			$output = '';
			foreach (str_split($input) as $c) {
				if ($c == $last) {
					$num++;
					continue;
				}

				if ($last != null) {
					$output .= $num . $last;
				}

				$last = $c;
				$num = 1;
			}
			$output .= $num . $last;
			$input = $output;

			if (isDebug()) { echo $input, "\n"; }
		}
		echo 'Length: ', strlen($input), "\n";
	}

	if (isTest()) {
		lookAndSay($input, 5);
	} else {
		lookAndSay($input, 40);
		lookAndSay($input, 50);
	}
