<?php
	/* Some of these are not memory efficient, so don't bother caring. */
	ini_set('memory_limit', '-1');

	/* Known Answers so far for comparing output after any changes. */
	require_once(dirname(__FILE__) . '/answers.php');

	/**
	 * Get the filen to read input from.
	 * This will return php://stdin if we have something passed on stdin,
	 * else it will return the file passed on the cli as --file if present, if
	 * no file specified on the CLI then test mode uses 'test.txt' otherwise
	 * fallback to 'input.txt'
	 *
	 * @return Filename to read from.
	 */
	function getInputFilename() {
		global $__CLIOPTS;

		if (!posix_isatty(STDIN)) {
			return 'php://stdin';
		} else if (isset($__CLIOPTS['file']) && file_exists($__CLIOPTS['file'])) {
			return $__CLIOPTS['file'];
		}

		$default = realpath(dirname($_SERVER['PHP_SELF'])) . '/' . basename(isTest() ? 'test.txt' : 'input.txt');
		if (file_exists($default)) {
			return $default;
		}

		die('No valid input found.');
	}

	/**
	 * Get the input as an array of lines.
	 *
	 * @return File as an array of lines. Empty lines are ignored.
	 */
	function getInputLines() {
		return file(getInputFilename(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	/**
	 * Get the input as a single string.
	 *
	 * @return Whole file as a single string.
	 */
	function getInputContent() {
		return file_get_contents(getInputFilename());
	}

	/**
	 * Get the first line from the input.
	 *
	 * @return First line of input.
	 */
	function getInputLine() {
		$lines = getInputLines();
		return isset($lines[0]) ? trim($lines[0]) : '';
	}

	/**
	 * Are we running in debug mode?
	 *
	 * Debug mode usually results in more output.
	 *
	 * @return True for debug mode, else false.
	 */
	function isDebug() {
		global $__CLIOPTS;

		return isset($__CLIOPTS['d']) || isset($__CLIOPTS['debug']);
	}

	/**
	 * Are we running in test mode?
	 *
	 * Test mode reads from test.txt not input.txt by default.
	 *
	 * @return True for test mode, else false.
	 */
	function isTest() {
		global $__CLIOPTS;

		return isset($__CLIOPTS['t']) || isset($__CLIOPTS['test']);
	}

	/**
	 * array_sum on multi-dimensional arrays.
	 *
	 * @param $array Array to sum.
	 * @return Sum of all vaules in array.
	 */
	function multi_array_sum($array) {
		$result = 0;
		foreach ($array as $a) { $result += (is_array($a) ? multi_array_sum($a) : $a); }
		return $result;
	}

	/**
	 * Get all the permutations of an array of items.
	 * (From: http://stackoverflow.com/a/13194803/310353)
	 *
	 * @param $items Items to get permutations of.
	 * @param $perms Ignore this param, used for recursion when caclulating permutations.
	 * @return All permutations of $items;
	 */
	function getPermutations($items, $perms = array()) {
		if (empty($items)) {
			$return = array($perms);
		} else {
			$return = array();
			for ($i = count($items) - 1; $i >= 0; --$i) {
				$newitems = $items;
				$newperms = $perms;
				list($foo) = array_splice($newitems, $i, 1);
				array_unshift($newperms, $foo);
				$return = array_merge($return, getPermutations($newitems, $newperms));
			}
		}
		return $return;
	}

	/**
	 * Get an ascii Wreath as a string.
	 *
	 * @param $colour Colourise the wreath.
	 * @return The wreath
	 */
	function getWreath($colour = true) {
			$canColour = $colour && (function_exists('posix_isatty') && posix_isatty(STDOUT)) || getenv('ANSICON') !== FALSE;

			if ($canColour) {
				$name = "\033[0m";
				$wreath = "\033[0;32m";
				$bow = "\033[1;31m";
				$berry = "\033[1;31m";
				$reset = "\033[0m";
			} else {
				$reset = $berry = $bow = $wreath = $name = '';
			}

			return <<<WREATH
$wreath           ,....,
$wreath        ,;;:${berry}o$wreath;;;${berry}o$wreath;;,
$wreath      ,;;${berry}o$wreath;'''''';;;;,
$wreath     ,;:;;        ;;${berry}o$wreath;,
$wreath     ;${berry}o$wreath;;          ;;;;
$wreath     ;;${berry}o$wreath;          ;;${berry}o$wreath;
$wreath     ';;;,  ${bow}_  _$wreath  ,;;;'
$wreath      ';${berry}o$wreath;;$bow/_\/_\\$wreath;;${berry}o$wreath;'
$name  jgs $wreath  ';;$bow\_\/_/$wreath;;'
$bow           '//\\\'
$bow           //  \\\ $reset  Advent of Code 2015
$bow          |/    \| $reset - ShaneMcC
$reset

WREATH;
	}

	try {
		$__CLIOPTS = @getopt("hdtw", array('help', 'file:', 'debug', 'test'));
		if (isset($__CLIOPTS['h']) || isset($__CLIOPTS['help'])) {
			echo getWreath(), "\n";
			echo 'Usage: ', $_SERVER['argv'][0], ' [options]', "\n";
			echo '', "\n";
			echo 'Valid options', "\n";
			echo '  -h, --help               Show this help output', "\n";
			echo '  -t, --test               Enable test mode (default to reading input from test.txt not input.txt)', "\n";
			echo '  -d, --debug              Enable debug mode', "\n";
			echo '      --file <file>        Read input from <file>', "\n";
			echo '', "\n";
			echo 'Input will be read from STDIN in preference to either <file> or the default files.', "\n";
			die();
		}
	} catch (Exception $e) { /* Do nothing. */ }
	if (!isset($__CLIOPTS['w'])) { echo getWreath(), "\n"; }
