<?php
	/* Some of these are not memory efficient, so don't bother caring. */
	ini_set('memory_limit', '-1');

	/**
	 * Get the filename to read input from.
	 * This will return php://stdin if we have something passed on stdin,
	 * else it will return the file passed in as $default.
	 *
	 * @param $default Default filename if stdin is not useful. ('input.txt' if blank)
	 * @return Filename to read.
	 */
	function getInputFilename($default = '') {
		return !posix_isatty(STDIN) ? 'php://stdin' : realpath(dirname($_SERVER['PHP_SELF'])) . '/' . basename(empty($default) ? 'input.txt' : $default);
	}

	/**
	 * Get the input as an array of lines.
	 *
	 * @param $default Default filename if stdin is not useful.
	 * @return File as an array of lines. Empty lines are ignored.
	 */
	function getInputLines($default = '') {
		return file(getInputFilename($default), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	/**
	 * Get the first line from the input.
	 *
	 * @param $default Default filename if stdin is not useful.
	 * @return First line of input.
	 */
	function getInputLine($default = '') {
		$lines = getInputLines($default);
		return trim($lines[0]);
	}
