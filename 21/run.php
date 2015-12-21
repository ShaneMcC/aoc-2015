#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$boss = array();
	foreach ($input as $details) {
		preg_match('#(.*): (.*)#SADi', $details, $m);
		list($all, $stat, $value) = $m;
		$boss[$stat] = $value;
	}

	$shop = array('Weapons' => array(), 'Armor' => array(), 'Rings' => array());

	$shop['Weapons']['Dagger'] = array('Name' => 'Dagger', 'Cost' => 8, 'Damage' => 4, 'Armor' => 0);
	$shop['Weapons']['Shortsword'] = array('Name' => 'Shortsword', 'Cost' => 10, 'Damage' => 5, 'Armor' => 0);
	$shop['Weapons']['Warhammer'] = array('Name' => 'Warhammer', 'Cost' => 25, 'Damage' => 6, 'Armor' => 0);
	$shop['Weapons']['Longsword'] = array('Name' => 'Longsword', 'Cost' => 40, 'Damage' => 7, 'Armor' => 0);
	$shop['Weapons']['Greataxe'] = array('Name' => 'Greataxe', 'Cost' => 74, 'Damage' => 8, 'Armor' => 0);

	$shop['Armor']['Leather'] = array('Name' => 'Leather', 'Cost' => 13, 'Damage' => 0, 'Armor' => 1);
	$shop['Armor']['Chainmail'] = array('Name' => 'Chainmail', 'Cost' => 31, 'Damage' => 0, 'Armor' => 2);
	$shop['Armor']['Splintmail'] = array('Name' => 'Splintmail', 'Cost' => 53, 'Damage' => 0, 'Armor' => 3);
	$shop['Armor']['Bandedmail'] = array('Name' => 'Bandedmail', 'Cost' => 75, 'Damage' => 0, 'Armor' => 4);
	$shop['Armor']['Platemail'] = array('Name' => 'Platemail', 'Cost' => 102, 'Damage' => 0, 'Armor' => 5);

	$shop['Rings']['Damage +1'] = array('Name' => 'Damage +1', 'Cost' => 25, 'Damage' => 1, 'Armor' => 0);
	$shop['Rings']['Damage +2'] = array('Name' => 'Damage +2', 'Cost' => 50, 'Damage' => 2, 'Armor' => 0);
	$shop['Rings']['Damage +3'] = array('Name' => 'Damage +3', 'Cost' => 100, 'Damage' => 3, 'Armor' => 0);
	$shop['Rings']['Defense +1'] = array('Name' => 'Defense +1', 'Cost' => 20, 'Damage' => 0, 'Armor' => 1);
	$shop['Rings']['Defense +2'] = array('Name' => 'Defense +2', 'Cost' => 40, 'Damage' => 0, 'Armor' => 2);
	$shop['Rings']['Defense +3'] = array('Name' => 'Defense +3', 'Cost' => 80, 'Damage' => 0, 'Armor' => 3);

	if (isTest()) {
		$player = array('Hit Points' => '8', 'Damage' => '5', 'Armor' => '5', 'Items' => array());
	} else {
		$player = array('Hit Points' => '100', 'Damage' => '0', 'Armor' => '0', 'Items' => array());
	}

	function calculateHit($attacker, $victim) {
		$damage = $attacker['Damage'];
		if (isset($attacker['Items'])) {
			foreach ($attacker['Items'] as $item) {
				$damage += $item['Damage'];
			}
		}


		$damage -= $victim['Armor'];
		if (isset($victim['Items'])) {
			foreach ($victim['Items'] as $item) {
				$damage -= $item['Armor'];
			}
		}

		return $damage;
	}

	function simulate($player, $boss) {
		if (isDebug()) {
			echo '==============================', "\n";
			echo 'FIGHT: ', "\n";
			print_r($player['Items']);
			echo 'COST: ', getCost($player['Items']), ".\n";
			echo '==============================', "\n";
		}

		while ($player['Hit Points'] > 0 && $boss['Hit Points'] > 0) {
			$bossDamage = calculateHit($player, $boss);
			$boss['Hit Points'] -= $bossDamage;
			if (isDebug()) { echo 'Boss takes ', $bossDamage, ' Damage: ', $boss['Hit Points'], "\n"; }
			if ($boss['Hit Points'] <= 0) {
				if (isDebug()) { echo 'Player Wins', "\n"; }
				return true;
			}

			$playerDamage = calculateHit($boss, $player);
			$player['Hit Points'] -= $playerDamage;
			if (isDebug()) { echo 'Player takes ', $playerDamage, ' Damage: ', $player['Hit Points'], "\n"; }
			if ($player['Hit Points'] <= 0) {
				if (isDebug()) { echo 'Boss Wins', "\n"; }
				return false;
			}
		}
	}

	function getCost($items) {
		$cost = 0;
		foreach ($items as $item) { $cost += $item['Cost']; }
		return $cost;
	}

	function getShopCombinations($shop) {
		foreach ($shop['Weapons'] as $weapon) {
			yield array($weapon);
			foreach ($shop['Armor'] as $armor) {
				yield array($weapon, $armor);
				foreach ($shop['Rings'] as $ring1) {
					yield array($weapon, $ring1);
					yield array($weapon, $armor, $ring1);
					foreach ($shop['Rings'] as $ring2) {
						if ($ring1['Name'] != $ring2['Name']) {
							yield array($weapon, $ring1, $ring2);
							yield array($weapon, $armor, $ring1, $ring2);
						}
					}
				}
			}
		}
	}


	function bestCombination($shop, $player, $boss) {
		$smallestCost = PHP_INT_MAX;
		$bestItems = array();

		foreach (getShopCombinations($shop) as $items) {
			$player['Items'] = $items;
			$cost = getCost($player['Items']);
			if (simulate($player, $boss) && $cost < $smallestCost) {
				$smallestCost = $cost;
				$bestItems = $items;
			}
		}

		return $bestItems;
	}

	function worstCombination($shop, $player, $boss) {
		$biggestCost = 0;
		$worstItems = array();

		foreach (getShopCombinations($shop) as $items) {
			$player['Items'] = $items;
			$cost = getCost($player['Items']);
			if (!simulate($player, $boss) && $cost > $biggestCost) {
				$biggestCost = $cost;
				$worstItems = $items;
			}
		}

		return $worstItems;
	}

	$best = bestCombination($shop, $player, $boss);
	echo 'Part 1: ', getCost($best), "\n";

	$worst = worstCombination($shop, $player, $boss);
	echo 'Part 2: ', getCost($worst), "\n";
