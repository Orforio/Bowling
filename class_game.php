<?php
/* class_player.php
 * ----------
 * Written by Richard Whittaker (richard@sblorgh.org) for the BBC Digital Graduate Scheme Assessment
 *
 * Contains the Game class for storing a game's players, as well as functions to determine a user's score and display a scoreboard
 */

require_once("class_player.php");
require_once("class_cli.php");

class Game {
	private $numberPlayers;
	private $players = array();
	private $cli;
	
	// __construct(): when a new game is started, ask the user for number of players and the names of those players
	function __construct() {
		$this->cli = new CLI();
	
		$this->numberPlayers = floor($this->askNumberPlayers());

		for ($i=1; $i <= $this->numberPlayers; $i++) {
			echo "Player " . $i . ": please enter your name." . "\n";
			$this->players[$i] = new Player($this->cli->getUserInput());
		}
		
		echo "Thank you. GET READY TO BOWL!" . "\n";
		echo "The game will now begin. Type 'scores' at any time to see the scoreboard." . "\n";
		echo "-= = = = =-" . "\n";
	}
	
	// askNumberPlayers(): asks the user for the number of players in the game
	private function askNumberPlayers() {
		echo "How many players are there? (1-6)" . "\n";

		$isNumberValid = false;
	
		do {
			$inputNumberPlayers = $this->cli->getUserInput();
		
			if (is_numeric($inputNumberPlayers) && $inputNumberPlayers >= 1 && $inputNumberPlayers <= 6) {	// Number of players must be between 1 and 6
				$isNumberValid = true;
			}
			else {
				echo "Sorry, number of players must be between 1 and 6. Please try again." . "\n";
			}
		}
		while (!$isNumberValid);
	
		return $inputNumberPlayers;
	}
	
	// getNumberPlayers(): gets the number of players in this game
	public function getNumberPlayers() {
		return $this->numberPlayers;
	}
	
	// inputScore(): takes arguments $currentPlayer and $currentFrame corresponding to the player currently bowling and the number of the frame; asks user for their score and updates that user's score array
	public function inputScore($currentPlayer, $currentFrame) {
		$isScore1Valid = false;
		$isScore2Valid = false;
		$isScore3Valid = false;
		$frame10BonusBall = false;
		
		echo "-= = = = =-" . "\n";
		echo "[FRAME: " . $currentFrame . "] - [PLAYER: " . $this->players[$currentPlayer]->getName() . "]" . "\n";
		
		do {
			echo $this->players[$currentPlayer]->getName() . ": please enter your score for the first ball." . "\n";
			$inputBall1Score = $this->cli->getUserInput();
			
			if ($inputBall1Score == "scores") {	// If user typed in "scores" then display scores instead
				$this->printScores($currentFrame);
			}
			elseif (is_numeric($inputBall1Score) && $inputBall1Score >= 0 && $inputBall1Score <= 10) {	// Ball1 score must be a number between 0 and 10
				$isScore1Valid = true;
				$inputBall1Score = floor($inputBall1Score);
			}
			else {
				echo "Sorry, score must be between 0 and 10." . "\n";
			}
		}
		while (!$isScore1Valid);
		
		if ($currentFrame == 10 && $inputBall1Score == 10) {	// If this is the final frame and the user has bowled a strike, they get a bonus ball
			$frame10BonusBall = true;
		}
		
		if ($inputBall1Score < 10 || $frame10BonusBall) {	// If the user did not bowl a strike, or if this is the last frame and the user bowled a strike on their first ball, we must process their second ball
			do {
				echo $this->players[$currentPlayer]->getName() . ": please enter your score for the second ball." . "\n";
				$inputBall2Score = $this->cli->getUserInput();
				
				if ($inputBall2Score == "scores") {
					$this->printScores($currentFrame);
				}
				elseif (is_numeric($inputBall2Score) && $frame10BonusBall && $inputBall2Score >= 0 && $inputBall2Score <= 10) {	// If this is the final frame and the user bowled a strike on their first ball, the second ball must be anumber between 0 and 10
					$isScore2Valid = true;
					$inputBall2Score = floor($inputBall2Score);
				}
				elseif (is_numeric($inputBall2Score) && $inputBall2Score >= 0 && $inputBall2Score <= (10 - $inputBall1Score)) {	// If this is a regular frame the second ball must be a number between 0 and 10 minus the previous ball
					$isScore2Valid = true;
					$inputBall2Score = floor($inputBall2Score);
				}
				elseif ($frame10BonusBall) {
					echo "Sorry, score must be between 0 and 10." . "\n";
				}
				else {
					echo "Sorry, score must be between 0 and " . (10 - $inputBall1Score) . "." . "\n";
				}
			}
			while (!$isScore2Valid);
		}
		else {	// First ball was a strike and this is not the final frame, so the second ball gets scored 0
			$inputBall2Score = 0;
		}
		
		if ($currentFrame == 10 && (($inputBall1Score + $inputBall2Score) == 10)) {	// If this is the last frame and the player bowled a spare, they get a bonus ball
			$frame10BonusBall = true;
		}
		
		if ($frame10BonusBall) {	// If this is the last frame and the user got a bonus third ball, we process it now
			do {
				echo $this->players[$currentPlayer]->getName() . ": please enter your score for the third ball." . "\n";
				$inputBall3Score = $this->cli->getUserInput();
				
				if ($inputBall3Score == "scores") {
					$this->printScores($currentFrame);
				}
				elseif (is_numeric($inputBall3Score) && $inputBall3Score >= 0 && $inputBall3Score <= 10) {	// Ball3 must be a number between 0 and 10
					$isScore3Valid = true;
					$inputBall3Score = floor($inputBall3Score);
				}
				else {
					echo "Sorry, score must be between 0 and 10." . "\n";
				}
			}
			while (!$isScore3Valid);
		}
		
		if ($inputBall1Score == 10 || ($frame10BonusBall && ($inputBall2Score == 10)) || ($frame10BonusBall && ($inputBall3Score == 10))) {
			echo "STRIKE!" . "\n";	// Congratulate the player on a strike
		}
		elseif (($inputBall1Score + $inputBall2Score) == 10) {
			echo "SPARE!" . "\n";	// Congratulate the player on a spare
		}
		
		$this->players[$currentPlayer]->addScore($currentFrame, $inputBall1Score, $inputBall2Score);	// Write the score to the user's scores array
		
		if ($frame10BonusBall) {
			$this->players[$currentPlayer]->addScore(11, $inputBall3Score, 0);	// A bonus ball in the last frame is added as frame 11
		}
		
		echo "-= = = = =-" . "\n";
		echo "\n";
	}
	
	// printScores(): will display the current scores
	public function printScores($currentFrame) {
		$this->calculateScores($currentFrame);
		
		echo "\n";
		if ($currentFrame == 11) {	// If game's over say "Final scores" instead of "frame 11"
			echo "--== FINAL SCORES ==--" . "\n";
		}
		else{
			echo "--== SCORES as of frame " . $currentFrame . " ==--" . "\n";
			echo "Scores for the current frame are not calculated." ."\n";
		}
		echo "\n";
		
		for ($printingPlayer = 1; $printingPlayer <= $this->numberPlayers; $printingPlayer++) {	// Cycle through the players in turn
			$playerTotal = 0;
			$playerScores = $this->players[$printingPlayer]->getScores();
			
			echo "[PLAYER " . $printingPlayer . ": " . $this->players[$printingPlayer]->getName() . "]" . "\n";
			echo "|";
			
			for ($printingFrame = 1; $printingFrame < $currentFrame; $printingFrame++) {	// Print the first line of scores, the actual pins down on each shot
				if ($printingFrame == 10) {	// Special display rules for frame 10
					$bonusBall = false;
					
					if ($playerScores[$printingFrame]["ball1"] == 10) {	// Strike in first ball means there will be a bonus ball
						echo " X ";
						$bonusBall = true;
					}
					else {
						echo " " . $playerScores[$printingFrame]["ball1"] . " ";
					}
					
					if ($playerScores[$printingFrame]["ball2"] == 10) {
						echo "X ";
					}
					elseif (($playerScores[$printingFrame]["ball1"] + $playerScores[$printingFrame]["ball2"]) == 10) {	// Spare on the second ball means there will be a bonus ball
						echo "/ ";
						$bonusBall = true;
					}
					else {
						echo $playerScores[$printingFrame]["ball2"] . " ";
					}
					
					if ($bonusBall) {	// Display the bonus ball
						if ($playerScores[$printingFrame+1]["ball1"] == 10) {
							echo "X ";
						}
						else {
							echo $playerScores[$printingFrame+1]["ball1"] . " ";
						}
					}
					else {	// If there's no bonus ball, add extra spaces to make scorecard line up
						echo "  ";
					}
					
					echo "|";
				}
				else {
					if ($playerScores[$printingFrame]["ball1"] == 10) {	// If score is a strike, display an X instead
						echo "  X  " . "|";
					}
					elseif (($playerScores[$printingFrame]["ball1"] + $playerScores[$printingFrame]["ball2"]) == 10) {	// If score is a spare, display first ball followed by a slash
						echo " " . $playerScores[$printingFrame]["ball1"] . " / " . "|";
					}
					else {	// Not a special score, display normally
						echo " " . $playerScores[$printingFrame]["ball1"] . " " . $playerScores[$printingFrame]["ball2"] . " " . "|";
					}
				}
			}
			
			echo "\n";
			echo "|";
			
			for ($printingFrame = 1; $printingFrame < $currentFrame; $printingFrame++) {	// Prints the second line of scores, the frame totals
				$playerTotal = $playerTotal + $playerScores[$printingFrame]["total"];
				if ($printingFrame == 10) {
					echo " " . $playerScores[$printingFrame]["total"];
					if ($playerScores[$printingFrame]["total"] >= 10) {	// Determines how many spaces to print to keep things lined up
						echo "    ";
					}
					else {
						echo "     ";
					}
				}
				else {
					echo " " . $playerScores[$printingFrame]["total"];
					if ($playerScores[$printingFrame]["total"] >= 10) {	// Determines how many spaces to print to keep things lined up
						echo "  ";
					}
					else {
						echo "   ";
					}
				}
				
				echo "|";
			}
			
			echo "\n";
			echo "TOTAL: " . $playerTotal;	// Prints the final score for the player
			
			echo "\n\n";
		}
	}
	
	// calculateScores(): adds up scores for each frame and writes these to the user's scores array
	private function calculateScores($currentFrame) {
		for ($calculatingPlayer = 1; $calculatingPlayer <= $this->numberPlayers; $calculatingPlayer++) {	// Cycle through each player in turn
			$playerScores = $this->players[$calculatingPlayer]->getScores();
			// TESTING // print_r($playerScores);
			
			for ($calculatingFrame = 1; $calculatingFrame < $currentFrame; $calculatingFrame++) {	// Cycle through each frame in turn
				if ($calculatingFrame == 10) {	// Special rules for frame 10
					if ($playerScores[$calculatingFrame]["ball1"] == 10) {	// If player got a strike on the first ball of frame 10...
						$currentTally = 10 + $playerScores[$calculatingFrame]["ball2"] + $playerScores[$calculatingFrame+1]["ball1"];	// Then they get a bonus for the next two balls
						$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
						// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";
					}
					elseif (($playerScores[$calculatingFrame]["ball1"] + $playerScores[$calculatingFrame]["ball2"]) == 10) {	// If player got a spare on the second ball of frame 10...
						$currentTally = 10 + $playerScores[$calculatingFrame+1]["ball1"];	// Then they get a bonus for the next ball
						$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
						// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";

					}
					else {	// Player got neither a strike nor a spare on frame 10
						$currentTally = $playerScores[$calculatingFrame]["ball1"] + $playerScores[$calculatingFrame]["ball2"];	// User gets normal scoring
						$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
						// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";

					}
				}
				elseif ($playerScores[$calculatingFrame]["ball1"] == 10) {	// Player bowled a strike
					if ($playerScores[$calculatingFrame+1]["ball1"] == 10) {	// If next frame's ball was also a strike...
						$currentTally = 20 + $playerScores[$calculatingFrame+2]["ball1"];	// Add the ball from the next frame along
						$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
						// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";

					}
					else {	// Next frame's first ball was not a strike...
						$currentTally = 10 + $playerScores[$calculatingFrame+1]["ball1"] + $playerScores[$calculatingFrame+1]["ball2"];	// ... so just add the next frame's balls normally
						$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
						// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";
					}
				}
				elseif (($playerScores[$calculatingFrame]["ball1"] + $playerScores[$calculatingFrame]["ball2"]) == 10) {	// If player bowled a spare...
					$currentTally = 10 + $playerScores[$calculatingFrame+1]["ball1"];	// Then add next frame's first ball
					$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
					// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";

				}
				else {	// Player bowled neither a strike nor a spare, nor is it frame 10
					$currentTally = $playerScores[$calculatingFrame]["ball1"] + $playerScores[$calculatingFrame]["ball2"];	// Add this frame's balls, normal scoring
					$this->players[$calculatingPlayer]->addTally($calculatingFrame, $currentTally);
					// TESTING // echo "Tally for " . $this->players[$calculatingPlayer-1]->getName() . ", frame " . $calculatingFrame . " is " . $currentTally . "\n";

				}
			}
		}
	}
}
?>