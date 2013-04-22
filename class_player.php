<?php
/* class_player.php
 * ----------
 * Written by Richard Whittaker (richard@sblorgh.org) for the BBC Digital Graduate Scheme Assessment
 *
 * Contains the Player class for storing a player's name and score, as well as updating the score
 */

class Player {
	private $name;
	private $scores = array();
	
	// __construct(): takes argument $playerName and sets it as the player's name when the object is created
	function __construct($playerName) {
		$this->name = $playerName;
	}
	
	// getName(): returns the player's name
	public function getName() {
		return $this->name;
	}
	
	// getScores(): returns an array of the player's scores
	public function getScores() {
		// TESTING // print_r($this->scores);
		return $this->scores;
	}
	
	// addScore(): takes arguments $frame, $ball1 and $ball2 corresponding to the two balls in a given frame and adds those scores to the player's $scores array
	public function addScore($frame, $ball1, $ball2) {
		$this->scores[$frame] = array("ball1" => $ball1, "ball2" => $ball2, "total" => 0);	// The third value is the frame's total score, calculated by the Game class
		// TESTING // echo "Here's how my scores look now: " . print_r($this->scores) . "\n";
	}
	
	// addTally(): takes arguments $frame and $total corresponding to the frame to add the total score to
	public function addTally($frame, $total) {
		$this->scores[$frame]["total"] = $total;
		// TESTING // echo "Just added tally: " . $this->scores[$frame]["total"] . "\n";
	}
}
?>