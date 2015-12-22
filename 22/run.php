#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$boss = array();
	$boss['Name'] = 'Boss';
	$boss['Armor'] = '0';
	$boss['Hit Points'] = '0';
	$boss['Damage'] = '0';
	$boss['Mana'] = '0';
	foreach ($input as $details) {
		preg_match('#(.*): (.*)#SADi', $details, $m);
		list($all, $stat, $value) = $m;
		$boss[$stat] = $value;
	}
	$boss['Effects'] = array();

	$spells = array();

	// Player Spells
	$spells['Magic Missile'] = array('Name' => 'Magic Missile', 'Mana' => 53, 'Damage' => 4);
	$spells['Drain'] = array('Name' => 'Drain', 'Mana' => 73, 'Damage' => 2, 'Heal' => 2);
	$spells['Shield'] = array('Name' => 'Shield', 'Mana' => 113, 'Ticks' => 6, 'Armor' => 7);
	$spells['Poison'] = array('Name' => 'Poison', 'Mana' => 173, 'Ticks' => 6, 'Damage' => 3);
	$spells['Recharge'] = array('Name' => 'Recharge', 'Mana' => 229, 'Ticks' => 5, 'Regain' => 101);

	// Boss Spell
	$spells['Hit'] = array('Name' => 'Hit', 'Mana' => 0, 'Damage' => 8);

	if (isTest()) {
		$player = array('Name' => 'Player', 'Hit Points' => '10', 'Mana' => '250', 'Armor' => 0, 'Effects' => array());
	} else {
		$player = array('Name' => 'Player', 'Hit Points' => '50', 'Mana' => '500', 'Armor' => 0, 'Effects' => array());
	}


	function tick(&$target) {
		// Run current effects
		$newEffects = array();
		foreach ($target['Effects'] as $effect) {
			if (isset($effect['Damage'])) {
				$target['Hit Points'] -= $effect['Damage'];
				if (isDebug()) { echo $effect['Name'], ' deals ', $effect['Damage'], ' damage;'; }
			} else if (isset($effect['Regain'])) {
				$target['Mana'] += $effect['Regain'];
				if (isDebug()) { echo $effect['Name'], ' replenishes ', $effect['Regain'], ' mana;'; }
			} else {
				if (isDebug()) { echo $effect['Name'], ' is active;'; }
			}


			if ($effect['Ticks'] > 1) {
				$effect['Ticks']--;
				$newEffects[$effect['Name']] = $effect;
				if (isDebug()) { echo ' its timer is now ', $effect['Ticks'], '.', "\n"; }
			} else {
				// Undo buffs.
				if (isset($effect['Armor'])) { $target['Armor'] -= $effect['Armor']; }
				if (isDebug()) { echo ' its timer has now expired.', "\n"; }
			}
		}
		$target['Effects'] = $newEffects;
	}

	function cast(&$source, &$target, $spell) {
		// Run New Spell.
		$damage = 0;
		$heal = 0;
		if ($spell['Mana'] > 0) {
			if (isDebug()) { echo $source['Name'], ' casts ', $spell['Name']; }
		} else {
			if (isDebug()) { echo $source['Name'], ' attacks'; }
		}
		if (isset($spell['Ticks'])) {
			// Add buffs.
			if (isset($spell['Armor']) || isset($spell['Regain'])) {
				if (isset($spell['Armor'])) {
					$source['Armor'] += $spell['Armor'];
					if (isDebug()) { echo ', adding ', $spell['Armor'], ' armor', "\n"; }
				}
				if (isset($source['Effects'][$spell['Name']])) { if (isDebug()) { echo "\n"; } return FALSE; }
				$source['Effects'][$spell['Name']] = $spell;
			} else {
				if (isset($target['Effects'][$spell['Name']])) { if (isDebug()) { echo "\n"; } return FALSE; }
				$target['Effects'][$spell['Name']] = $spell;
			}
		} else {
			// Instant action attack.
			if (isset($spell['Damage'])) { $damage += $spell['Damage']; }
			if (isset($spell['Heal'])) { $heal += $spell['Heal']; }
		}
		if ($source['Mana'] < $spell['Mana']) { if (isDebug()) { echo "\n"; } return FALSE; }
		$source['Mana'] -= $spell['Mana'];

		if ($damage > 0) {
			$target['Hit Points'] -= max(1, $damage - $target['Armor']);
			if (isDebug()) { echo ', dealing ', max(1, $damage - $target['Armor']), ' damage'; }
		}
		if ($heal > 0) {
			$source['Hit Points'] += $heal;
			if (isDebug()) { echo ', healing ', $heal, ' hit points'; }
		}
		if (isDebug()) { echo "\n"; }

		return TRUE;
	}


	function simulate($player, $boss, $playerCasts, $bossCasts, $hard = false) {
		global $spells;

		$turns = 0;
		$result = false;
		$manaCost = 0;
		$casts = array();
		while (true) {
			$playerSpell = $spells[$playerCasts[$turns % count($playerCasts)]];
			$bossSpell = $spells[$bossCasts[$turns % count($bossCasts)]];
			$manaCost += $playerSpell['Mana'];

			if (isDebug()) {
				echo '-- Player turn --', "\n";
				echo '- Player has ', $player['Hit Points'], ' hit points, ', $player['Armor'], ' armor, ', $player['Mana'], ' mana', "\n";
				echo '- Boss has ', $boss['Hit Points'], ' hit points.', "\n";
			}

			if ($hard) {
				$player['Hit Points'] -= 1;
				if ($player['Hit Points'] <= 0) { if (isDebug()) { echo 'Player died.', "\n"; } break; }
			}

			tick($player);
			if ($player['Hit Points'] <= 0) { if (isDebug()) { echo 'Player died.', "\n"; } break; }
			tick($boss);
			if ($boss['Hit Points'] <= 0) { $result = true; if (isDebug()) { echo 'Boss died.', "\n"; } break; }


			$castResult = cast($player, $boss, $playerSpell);
			if ($boss['Hit Points'] <= 0) { $result = true; if (isDebug()) { echo 'Boss died.', "\n"; } break; }
			if (!$castResult) { if (isDebug()) { echo 'Player lost by error.', "\n"; } break; }
			$casts[] = $playerSpell['Name'];


			if (isDebug()) {
				echo "\n";
				echo '-- Boss turn --', "\n";
				echo '- Player has ', $player['Hit Points'], ' hit points, ', $player['Armor'], ' armor, ', $player['Mana'], ' mana', "\n";
				echo '- Boss has ', $boss['Hit Points'], ' hit points.', "\n";
			}

			tick($player);
			if ($player['Hit Points'] <= 0) { if (isDebug()) { echo 'Player died.', "\n"; } break; }
			tick($boss);
			if ($boss['Hit Points'] <= 0) { $result = true; if (isDebug()) { echo 'Boss died.', "\n"; } break; }

			$castResult = cast($boss, $player, $bossSpell);
			if (!$castResult) { if (isDebug()) { echo 'Boss lost by error.', "\n"; } $result = true; break; }
			if ($player['Hit Points'] <= 0) { if (isDebug()) { echo 'Player died.', "\n"; } break; }

			if (isDebug()) { echo "\n"; }
			$turns++;
		}

		return array($result, $manaCost, $turns, $casts);
	}

	function generateCastSequence($length = 15) {
		global $spells;

		$sequence = array();

		for ($i = 0; $i < $length; $i++) {
			while (true) {
				// Pick a Spell.
				$spell = $spells[array_rand($spells)];

				// Ignore the Boss Spell
				if ($spell['Mana'] == 0) { continue; }

				// If the spell is an effect, check that we haven't cast it
				// within $ticks / 3 turns else the effect is still in place.
				if (isset($spell['Ticks'])) {
					$start = max(0, $i - ($spell['Ticks'] / 3));
					$past = array_slice($sequence, $start);

					if (in_array($spell['Name'], $past)) { continue; }
				}
				$sequence[] = $spell['Name'];
				break;
			}
		}

		return $sequence;
	}

	function getBest($player, $boss, $games, $hard = false) {
		$lowestMana = PHP_INT_MAX;
		$bestSequence = array();

		for ($i = 0; $i < $games; $i++) {
			$playerCasts = generateCastSequence();
			$bossCasts = array('Hit');
			list($result, $manaCost, $turns, $casts) = simulate($player, $boss, $playerCasts, $bossCasts, $hard);

			if ($result) {
				if (isDebug()) { echo 'Won in ', $turns, ' turns with ', $manaCost, ' mana spent.', "\n"; }
				if ($manaCost < $lowestMana) {
					$lowestMana = $manaCost;
					$bestSequence = $casts;
				}
			} else {
				if (isDebug()) { echo 'Lost after ', $turns, ' turns with ', $manaCost, ' mana spent.', "\n"; }
			}
		}

		return array($lowestMana, $bestSequence);
	}

	$attempts = 50000;

	echo 'Part 1: ';
	list($lowestMana, $bestSequence) = getBest($player, $boss, $attempts, false);
	if (count($bestSequence) > 0) {
		echo 'Lowest calculated mana win: ', $lowestMana, "\n";
		echo 'Sequence: ', implode(', ', $bestSequence), "\n";
	} else {
		echo 'Didn\'t find a winnning combination.', "\n";
	}

	echo "\n";

	echo 'Part 2: ';
	list($lowestMana, $bestSequence) = getBest($player, $boss, $attempts, true);
	if (count($bestSequence) > 0) {
		echo 'Lowest calculated mana win: ', $lowestMana, "\n";
		echo 'Sequence: ', implode(', ', $bestSequence), "\n";
	} else {
		echo 'Didn\'t find a winnning combination.', "\n";
	}
