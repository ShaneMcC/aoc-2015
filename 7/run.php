#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	function process($lines) {
		$gates = array();

		foreach ($lines as $line) {
			if (preg_match('#(.*) (OR|AND|LSHIFT|RSHIFT) (.*) -> (.*)#SAD', $line, $matches)) {
				list($all, $input, $action, $value, $gate) = $matches;
			} else if (preg_match('#(NOT) (.*) -> (.*)#SAD', $line, $matches)) {
				list($all, $action, $value, $gate) = $matches;
				$input = null;
			} else if (preg_match('#(.*) -> (.*)#SAD', $line, $matches)) {
				list($all, $value, $gate) = $matches;
				$action = 'SET';
				$input = null;
			}

			$gates[$gate] = array('action' => $action, 'value' => $value, 'input' => $input);
		}

		return $gates;
	}

	function getValue(&$gates, $gate) {
		if (isset($gates[$gate]['finalvalue'])) {
			return $gates[$gate]['finalvalue'];
		}

		$action = $gates[$gate]['action'];
		$value = $gates[$gate]['value'];
		$input = $gates[$gate]['input'];

		$valueIsGate = preg_match('#[a-z]+#SAD', $value);
		$value = $valueIsGate ? getValue($gates, $value) : $value;

		if ($input != null) {
			$inputIsGate = preg_match('#[a-z]+#SAD', $input);
			$input = $inputIsGate ? getValue($gates, $input) : $input;
		}

		if ($action == 'SET') {
			$finalValue = $value;
		} else if ($action == 'AND') {
			$finalValue = ($input & $value);
		} else if ($action == 'OR') {
			$finalValue = ($input | $value);
		} else if ($action == 'NOT') {
			$finalValue = abs((~ 65535) - (~ $value));
		} else if ($action == 'LSHIFT') {
			$finalValue = ($input << $value);
		} else if ($action == 'RSHIFT') {
			$finalValue = ($input >> $value);
		} else {
			die('Unknown');
		}

		if (isDebug()) {
			echo sprintf('%s -> %s%s %s -> %s', $gate, ($input !== null ? (int)$input. ' ' : ''), $action, $value, $finalValue), "\n";
		}

		$gates[$gate]['finalvalue'] = (int)$finalValue;
		return (int)$finalValue;
	}


	$gates = process($lines);

	if (isDebug()) {
		foreach (array_keys($gates) as $key) {
			echo $key, ': ', getValue($gates, $key), "\n";
		}
	}

	// Actual challenge requires an 'a' gate.
	if (isset($gates['a'])) {
		echo "\n";
		// Original A
		echo 'Original a: ', getValue($gates, 'a'), "\n";
		// Store A.
		$a = getValue($gates, 'a');
		// Reset the gates.
		$gates = process($lines);
		// Change B
		$gates['b'] = array('action' => 'SET', 'value' => $a, 'input' => null);
		// New A
		echo 'Second a: ', getValue($gates, 'a'), "\n";
	}
