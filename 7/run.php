#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$lines = getInputLines();

	/**
	 * Logic Gates.
	 */
	class LogicGates {
		/** Store our gates. */
		private $gates = array();

		/**
		 * Create a new LogicGates class from the given list of connections.
		 *
		 * @param $connections List of connections.
		 */
		public function __construct($connections = array()) {
			foreach ($connections as $line) {
				if (preg_match('#(?:(.*)( OR| AND| LSHIFT| RSHIFT|NOT) )?(.*) -> (.*)#SAD', $line, $matches)) {
					list($all, $input, $action, $value, $gate) = $matches;
				}
				$this->set($gate, $value, $action, $input);
			}
		}

		/**
		 * Check if we know about a given gate.
		 *
		 * @param $gate Gate to check
		 * @return True if this is a valid gate, else false.
		 */
		public function has($gate) {
			return isset($this->gates[$gate]);
		}

		/**
		 * Get a list of known gates.
		 *
		 * @return List of known gates.
		 */
		public function gates() {
			$result = array_keys($this->gates);
			sort($result);
			return $result;
		}

		/**
		 * Get the value for a gate.
		 *
		 * If $gate is not a valid gate then it is assumed to be a raw value and
		 * is returned as-is.
		 * Values for gates are calculated lazily when they are first requested.
		 *
		 * @param $gate Gate to get value of.
		 * @return Value of gate.
		 */
		public function get($gate) {
			// If this is not a known gate, it's probably a raw value
			if (!isset($this->gates[$gate])) { return $gate; }

			// If we already know the final value for this gate, don't
			// calculate it again.
			if (isset($this->gates[$gate]['final'])) { return $this->gates[$gate]['final']; }

			// Get the various parameters of this connection.
			$input = isset($this->gates[$gate]['input']) ? $this->get($this->gates[$gate]['input']) : null;
			$action = isset($this->gates[$gate]['action']) ? $this->gates[$gate]['action'] : null;
			$value = $this->get($this->gates[$gate]['value']);

			// Do stuff.
			if ($action == 'AND') { $final = ($input & $value); }
			else if ($action == 'OR') { $final = ($input | $value); }
			else if ($action == 'NOT') { $final = abs((~ 65535) - (~ $value)); }
			else if ($action == 'LSHIFT') { $final = ($input << $value); }
			else if ($action == 'RSHIFT') { $final = ($input >> $value); }
			else if ($action == null) { $final = $value; }

			// Debugging
			if (isDebug()) {
				echo sprintf('%s -> %s%s%s -> %s', $gate, ($input !== null ? (int)$input. ' ' : ''), ($action !== null ? $action. ' ' : ''), $value, $final), "\n";
			}

			// Store value to save us recalculating again.
			$this->gates[$gate]['final'] = (int)$final;

			// Return new value.
			return $this->gates[$gate]['final'];
		}

		/**
		 * Setup $gate
		 *
		 * This will cause any gates that rely on this to require their value
		 * recalculating.
		 *
		 * NOTE: Setting looks backwards, eg:
		 * 	   "x AND y -> d" would be $gates->set('d', 'y', 'AND', 'x');
		 *
		 * @param $gate Gate to change.
		 * @param $value Value to set gate to.
		 * @param $action [Optional] Action for this gate.
		 * @param $input [Optional] Input for this gate.
		 */
		public function set($gate, $value, $action = '', $input = '') {
			$this->invalidate($gate);

			$this->gates[$gate] = array();
			if (!empty($input)) { $this->gates[$gate]['input'] = $input; }
			if (!empty($action)) { $this->gates[$gate]['action'] = trim($action); }
			$this->gates[$gate]['value'] = $value;
		}

		/**
		 * Invalidate the calculated final value of $gate and any gates that
		 * rely on it.
		 *
		 * @param $gate Gate to invalidate.
		 */
		private function invalidate($gate) {
			if (isset($this->gates[$gate]['final'])) {
				unset($this->gates[$gate]['final']);
				foreach ($this->gates as $g => $data) {
					if ($data['value'] == $gate || (isset($data['input']) && $data['input'] == $gate)) {
						$this->invalidate($g);
					}
				}
			}
		}
	}

	$gates = new LogicGates($lines);

	if (!isDebug()) {
		// foreach ($gates->gates() as $gate) { echo $gate, ': ', $gates->get($gate), "\n"; }
	}

	// Actual challenge requires an 'a' gate.
	if ($gates->has('a')) {
		echo "\n";
		// Original A
		$a = $gates->get('a');
		echo 'Original a: ', $a, "\n";
		// Change B
		$gates->set('b', $a);
		// New A
		echo 'Second a: ', $gates->get('a'), "\n";
	}

