#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getHouses($maxHouses, $deliver = 10, $visitLimit = 0) {
		$houses = array();
		for ($h = 1; $h < $maxHouses; $h++) { $houses[$h] = 0; }

		for ($elf = 1; $elf < $maxHouses; $elf++) {
			for ($house = $elf; $house < $maxHouses; $house += $elf) {
				if ($visitLimit == 0 || $house < ($elf * $visitLimit)) {
					$houses[$house] += $elf * $deliver;
				} else { continue 2; }
			}
		}
		return $houses;
	}

	function getBestHouse($wantedCount, $deliver = 10, $visitLimit = 0) {
		$minElves = 3;
		while ($minElves > 0) {
			$houses = getHouses(floor($wantedCount / ($deliver * $minElves)), $deliver, $visitLimit);
			foreach ($houses as $house => $count) {
				if ($count > $wantedCount) {
					return $house;
				}
			}
			$minElves--;
		}
		return -1;
	}

	echo 'Part 1: ', getBestHouse($input, 10, 0), "\n";
	echo 'Part 2: ', getBestHouse($input, 11, 50), "\n";
