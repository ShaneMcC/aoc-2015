#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getHouses($maxHouses, $deliver = 10, $visitLimit = 0) {
		$houses = array();
		for ($elf = 1; $elf < $maxHouses; $elf++) {
	    	for ($house = $elf; $house < $maxHouses; $house += $elf) {
	    		if (!isset($houses[$house])) { $houses[$house] = 0; }

	    		if ($visitLimit == 0 || $house < ($elf * $visitLimit)) {
	        		$houses[$house] += $elf * $deliver;
	        	}
	        }
	    }
	    return $houses;
	}

	$houses = getHouses($input / 10, 10, 0);
    foreach ($houses as $house => $count) {
    	if ($count > $input) {
    		echo 'Part 1: ', $house, "\n";
    		break;
    	}
    }

    $houses = getHouses($input / 10, 11, 50);
    foreach ($houses as $house => $count) {
    	if ($count > $input) {
    		echo 'Part 2: ', $house, "\n";
    		break;
    	}
    }

