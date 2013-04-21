<?php
/* bowling.php
 * ----------
 * Written by Richard Whittaker (richard@sblorgh.org) for the BBC Digital Graduate Scheme Assessment
 *
 * Run this file in the PHP command line to start the bowling game:
 * > php bowling.php
 */

require_once("class_game.php");

echo "BOWLING SCOREKEEPER" . "\n";
echo "Written by Richard Whittaker" . "\n";
echo "(richard@sblorgh.org)" . "\n";
echo "For the BBC Digital Graduate Scheme Assessment" . "\n";
echo "-= = = = =-" . "\n";
echo "\n";

$theGame = new Game();

$numberPlayers = $theGame->getNumberPlayers();

for ($currentFrame = 1; $currentFrame <= 10; $currentFrame++) {	// Game loop - goes through each player and asks them for each frame's score
	for ($currentPlayer = 1; $currentPlayer <= $numberPlayers; $currentPlayer++) {
		$theGame->inputScore($currentPlayer, $currentFrame);
	}
}

echo "-= = = = =-" . "\n";
echo "GAME OVER!" . "\n";
echo "Here are the final scores..." . "\n";

$theGame->printScores(11);	// Sending 11 as the currentFrame to signify the game is over and all scores should be calculated

?>