<?php
	/* Some of these are not memory efficient, so don't bother caring. */
	ini_set('memory_limit', '-1');

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
	 * Get the first line from the input.
	 *
	 * @return First line of input.
	 */
	function getInputLine() {
		$lines = getInputLines();
		return trim($lines[0]);
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

	try { $__CLIOPTS = @getopt("dt", array('file:', 'debug', 'test')); } catch (Exception $e) { /* Do nothing. */ }
