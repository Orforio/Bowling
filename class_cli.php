<?php
/* class_cli.php
 * ----------
 * Written by Richard Whittaker (richard@sblorgh.org) for the BBC Digital Graduate Scheme Assessment
 *
 * Contains the CLI class for handling user input via the command-line interface
 */

class CLI {

	private $handle;
	private $line;

	// getUserInput(): opens STDIN to get one line of text back from the user via the CLI, and returns it
	public function getUserInput() {
		$this->handle = fopen("php://stdin", "r");		
		return $this->line = trim(fgets($this->handle));
	}
}
?>