#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$data = array();
	foreach ($input as $lines) {
		if (preg_match('#([a-z]{3}) ([^,]+)(?:, (.*))?#SADi', $lines, $m)) {
			$data[] = array($m[1], array_slice($m, 2));
		}
	}

	/**
	 * Simple 2-register, 6-instruction VM for Day 23.
	 */
	class VM {
		/** Current location. */
		private $location = -1;

		/** Known Instructions. */
		private $instrs = array();

		/** Internal Registers. */
		private $registers = array('a' => 0, 'b' => 0);

		/** Data to execute. */
		private $data = array();

		/**
		 * Create a new VM.
		 *
		 * @param $data (Optional) Program execution data.
		 */
		function __construct($data = array()) {
			$this->init();
			$this->loadProgram($data);
		}

		/**
		 * Load in a new program and reset the VM State.
		 *
		 * @param $data Data to load.
		 */
		function loadProgram($data) {
			$this->data = $data;
			$this->location = -1;
			$this->registers = array('a' => 0, 'b' => 0);
		}

		/**
		 * Get the instruction function by the given name.
		 *
		 * @param $instr Instruction name.
		 * @return Instruction function.
		 */
		private function getInstr($instr) {
			if (isset($this->instrs[$instr])) { return $this->instrs[$instr]; }
			throw new Exception('Unknown Instr: ' . $instr);
		}

		/**
		 * Iniit the Instructions.
		 */
		private function init() {
			/**
			 * hlf
			 *   - hlf r
			 *
			 * Sets register r to half its current value.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['hlf'] = function($vm, $args) {
				debugOut('hlf, [', implode(' ', $args), ']', "\n");
				$reg = $args[0];
				$val = $vm->getReg($reg) / 2;
				$vm->setReg($reg, $val);
			};

			/**
			 * tpl
			 *   - tpl r
			 *
			 * Sets register r to triple its current value
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['tpl'] = function($vm, $args) {
				debugOut('tpl, [', implode(' ', $args), ']', "\n");
				$reg = $args[0];
				$val = $vm->getReg($reg) * 3;
				$vm->setReg($reg, $val);
			};

			/**
			 * inc
			 *   - inc r
			 *
			 * Increments register r, adding 1 to it
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['inc'] = function($vm, $args) {
				debugOut('inc, [', implode(' ', $args), ']', "\n");
				$reg = $args[0];
				$val = $vm->getReg($reg) + 1;
				$vm->setReg($reg, $val);
			};

			/**
			 * jmp
			 *   - jmp offset
			 *
			 * Jump offset away from the current program location.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['jmp'] = function($vm, $args) {
				debugOut('jmp, [', implode(' ', $args), ']', "\n");

				$jmp = $vm->getInstr('__jmp');
				$jmp($vm, $args);
			};

			/**
			 * Implement jmp (also used by jie and jio)
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['__jmp'] = function($vm, $args) {
				$loc = $args[0];
				preg_match('#([+-])([0-9]+)#SADi', $loc, $bits);
				$curloc = $vm->getLocation();

				if ($bits[1] == '+') {
					$curloc += $bits[2];
				} else if ($bits[1] == '-') {
					$curloc -= $bits[2];
				}

				$this->jump($curloc - 1); // (-1 because step() always does +1)
			};

			/**
			 * jie
			 *   - jie r, offset
			 *
			 * Like jmp, but only jumps if register r is even
			 * ("jump if even")
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['jie'] = function($vm, $args) {
				debugOut('jie, [', implode(' ', $args), ']', "\n");
				$jump = 0;
				$reg = $vm->getReg($args[0]);

				if ($reg % 2 == 0) {
					$jmp = $vm->getInstr('__jmp');
					$jmp($vm, array($args[1]));
				}
			};

			/**
			 * jio
			 *   - jio r, offset
			 *
			 * Like jmp, but only jumps if register r is 1
			 * ("jump if one", not odd).
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['jio'] = function($vm, $args) {
				debugOut('jio, [', implode(' ', $args), ']', "\n");
				$jump = 0;
				$reg = $vm->getReg($args[0]);

				if ($reg == 1) {
					$jmp = $vm->getInstr('__jmp');
					$jmp($vm, array($args[1]));
				}
			};
		}

		/**
		 * Get the current execution location.
		 *
		 * @return Location of current execution.
		 */
		function getLocation() {
			return $this->location;
		}

		/**
		 * Jump to specific location.
		 *
		 * @param $loc Location to jump to.
		 */
		function jump($loc) {
			$this->location = $loc;
		}

		/**
		 * Step a single instruction.
		 *
		 * @return True if we executed something, else false if we have no more
		 *         to execute.
		 */
		function step() {
			if (isset($this->data[$this->location + 1])) {
				$this->location++;
				$next = $this->data[$this->location];

				$instr = $next[0];
				$data = $next[1];

				$ins = $this->getInstr($instr);
				$ins($this, $data);

				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * Continue stepping through untill we reach the end.
		 */
		function run() {
			while ($this->step()) { }
		}

		/**
		 * Get the value of the given register.
		 *
		 * @param $reg Register to get value of
		 * @return Value of $reg
		 */
		function getReg($reg) {
			if (isset($this->registers[$reg])) { return $this->registers[$reg]; }
			throw new Exception('Unknown Register: ' . $reg);
		}

		/**
		 * Set the value of the given register.
		 *
		 * @param $reg Register to Set value of
		 * @param $val Value to set register to.
		 */
		function setReg($reg, $val) {
			if (isset($this->registers[$reg])) { $this->registers[$reg] = $val; return $val; }
			throw new Exception('Unknown Register: ' . $reg);
		}
	}

	$vm = new VM($data);
	$vm->run();
	echo 'Part 1: [A: ', $vm->getReg('a'), '] [B: ', $vm->getReg('b'), ']', "\n";

	debugOut("\n\n");

	$vm->loadProgram($data);
	$vm->setReg('a', 1);
	$vm->run();
	echo 'Part 2: [A: ', $vm->getReg('a'), '] [B: ', $vm->getReg('b'), ']', "\n";
