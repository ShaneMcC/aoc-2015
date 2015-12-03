#!/usr/bin/php
<?php
	function run($directions, $people = 1) {
		// Create initial locations and deliver first present.
		$x = $y = array();
		$presents = array(0 => array(0 => 0));
		for ($i = 0; $i < $people; $i++) {
			$presents[0][0]++;
			$x[$i] = $y[$i] = 0;
		}
		$houses = 1;

		$person = 0;
		foreach (str_split($directions) as $bit) {
			// Move
			if ($bit == '^') { $y[$person]++; }
			else if ($bit == 'v') { $y[$person]--; }
			else if ($bit == '>') { $x[$person]++; }
			else if ($bit == '<') { $x[$person]--; }

			// Create a house if one doesn't already exist
			if (!isset($presents[$x[$person]][$y[$person]])) {
				$presents[$x[$person]][$y[$person]] = 0;
				$houses++;
			}

			// Deliver a present.
			$presents[$x[$person]][$y[$person]]++;

			// Alternate who delivers.
			$person = ($person + 1) % $people;
		}

		return $houses;
	}

	$directions = trim(file_get_contents('php://STDIN'));
	echo 'Houses with presents (Santa Only): ', run($directions, 1), "\n";
	echo 'Houses with presents (Santa + Robo Santa): ', run($directions, 2), "\n";

