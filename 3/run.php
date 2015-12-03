#!/usr/bin/php
<?php
	function run($directions, $robot = false) {
		$x = array('s' => 0, 'r' => 0);
		$y = array('s' => 0, 'r' => 0);
		$presents = array();
		$who = 's';

		// Starting house gets 2 presents.
		$presents[$x[$who]][$y[$who]] = 2;
		$houses = 1;

		foreach (str_split($directions) as $bit) {
			// Move
			if ($bit == '^') { $y[$who]++; }
			else if ($bit == 'v') { $y[$who]--; }
			else if ($bit == '>') { $x[$who]++; }
			else if ($bit == '<') { $x[$who]--; }

			// Create a house if one doesn't already exist
			if (!isset($presents[$x[$who]][$y[$who]])) {
				$presents[$x[$who]][$y[$who]] = 0;
				$houses++;
			}

			// Deliver a present.
			$presents[$x[$who]][$y[$who]]++;

			// Alternate who delivers.
			if ($robot) { $who = ($who == 's') ? 'r' : 's'; }
		}

//		var_dump($presents);
//		echo "\n\n";
		return $houses;
	}

	$directions = trim(file_get_contents('php://STDIN'));
	echo 'Houses with presents year 1: ', run($directions, false), "\n";
	echo 'Houses with presents year 2: ', run($directions, true), "\n";

?>
