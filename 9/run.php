#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	$places = array();
	foreach ($lines as $direction) {
		preg_match('#(.*) to (.*) = (.*)#', $direction, $matches);
		list($all, $source, $destination, $distance) = $matches;
		$places[$source][$destination] = $distance;
		$places[$destination][$source] = $distance;
	}
	foreach ($places as $k => $v) { asort($v); $places[$k] = $v; }

	function getShortJourney($start, $places) {
		$visted = array($start);
		$current = $start;
		$route = array();
		$routeDistance = 0;

		while (count($visted) != count($places)) {
			if (isDebug()) { echo '[', count($visted), '/', count($places), '] Starting from: ', $current, "\n"; }
			foreach ($places[$current] as $destination => $distance) {
				if (isDebug()) { echo "\t", 'Testing: ', $destination, "\n"; }
				if (!in_array($destination, $visted)) {
					if (isDebug()) { echo "\t\t", 'Unvisted!', "\n"; }
					$route[] = array('source' => $current, 'destination' => $destination, 'distance' => $distance);
					$routeDistance += $distance;

					$visted[] = $destination;
					$current = $destination;
					break;
				}
			}
		}

		return array($routeDistance, $route);
	}

	function sortByDistance($a, $b) {
		if ($a['distance'] == $b['distance']) { return 0; }
		else { return ($a['distance'] < $b['distance']) ? -1 : 1; }
	}

	$possible = array();
	foreach (array_keys($places) as $p) {
		list($distance, $route) = getShortJourney($p, $places);
		$possible[] = array('route' => $route, 'distance' => $distance);
	}
	usort($possible, 'sortByDistance');
	echo "\n\n";
	echo 'Naive Shortest Route: ', print_route($possible[0]['route']), "\n";
	echo 'Naive Shortest Distance: ', $possible[0]['distance'], "\n";

	function getAllRoutes($places) {
		$routes = array();
		foreach (getPermutations(array_keys($places)) as $journey) {
			$route = array();
			$distance = 0;
			$last = null;
			for ($i = 1; $i < count($journey); $i++) {
				$route[] = array('source' => $journey[$i-1], 'destination' => $journey[$i], 'distance' => $places[$journey[$i-1]][$journey[$i]]);
				$distance += $places[$journey[$i-1]][$journey[$i]];
			}
			$routes[] = array('route' => $route, 'distance' => $distance);
		}

		return $routes;
	}

	$possible = getAllRoutes($places);
	usort($possible, 'sortByDistance');

	function print_route($route) {
		$last = '';

		foreach ($route as $bit) {
			echo $bit['source'], ' -{', $bit['distance'], '}-> ';
			$last = $bit['destination'];
		}

		echo $last;
	}

	if (isDebug()) {
		$i = 0;
		foreach ($possible as $r) {
			echo $i++, ': ', print_route($r['route']), ' = ', $r['distance'], "\n";
		}
	}

	echo "\n";
	echo 'All Shortest Route: ', print_route($possible[0]['route']), "\n";
	echo 'All Shortest Distance: ', $possible[0]['distance'], "\n";
	echo "\n";
	echo 'Longest Route: ', print_route($possible[count($possible) - 1]['route']), "\n";
	echo 'Longest Distance: ', $possible[count($possible) - 1]['distance'], "\n";


