<?php

/*
* Title: index.php
* Abstract: Code is intended to work as a Tic Tac Toe Game. It is a Slack coding exercise.
*           Game will allow a user to call another user through a slash command. The first game will
*           allow let the first user play and then the second user will follow. Game will only allow
*           for the user who's turn it is to take the turn. Game will notify when game has ended
*           either for a winning move or if there are no more available spaces. There can be a game
*           played on each channel identified through the channel_id. 
* Author: Brayanne Reyes Ron
* Date 07/29/2016
*
*/

include 'completedGame.php';

//these are values that are sent from Slack to server
//$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];
//$team_id = $_POST['team_id'];
//$team_domain = $_POST['team_domain'];
$channel_id = $_POST['channel_id'];
//$channel_name = $_POST['channel_name'];
//$user_id = $_POST['user_id'];
$user_name = $_POST['user_name'];
//$response_url = $_POST['response_url'];

//checking if token belongs to our team
if($token != 'cKJQTLAulMirApeUCbyMkfT9'){ 
  $msg = "The token for the slash command doesn't match. Check your script.";
  die($msg);
  echo $msg;
}

//variable will hold message to send back to Slack
$finalMessage = ""; 

function displayBoard($channel_id){
			//to display table retrieve the values from grid
			$sql = "SELECT * FROM grid WHERE channel_id = '" . $channel_id . "'";
			$matrix = sendQuery($sql, NULL);

			$count = 0; //count to know when to display row names
			$finalMessage = ":slack::one::two::three:\n:a:"; //will add the columns names
			foreach($matrix as $key=>$value){
				if($key != 'channel_id'){ //to check all values except that of channel_id
					if($value == NULL){ $finalMessage .= ":anger:";} //will add the emoji of indicating available space
					elseif($value == 1){ $finalMessage .= ":x:";}//indicate player 1 spaces selected
					else{$finalMessage .= ":o:";} //indicate player 2 spaces selected
					
					$count++;

					if($count % 3 == 0){$finalMessage .= "\n";}//every 3, go to next line

					//needed to add the row letters in front of each row
					if($count == 3){$finalMessage .= ":b:";}
					elseif ($count == 6) {$finalMessage .= ":o2:";}
				}
			}
			return $finalMessage;
}

//will send a message to be displayed on Slack
function sendMessage($message){
	$data = array("response_type"=>"in_channel", "text"=> $message);
	echo json_encode($data);
	header("Content-Type: application/json");
}

//will return the person's who's next turn is
function checkNextTurn($channel_id){
		$sql = "SELECT turn FROM players WHERE channel_id = '" . $channel_id . "'" ;
		$turn = sendQuery($sql, NULL);
		return $turn['turn'];
}

//array holding the possible values for the grid
$possible_input = array("a1", "a2", "a3", "b1", "b2", "b3", "o1", "o2", "o3");

if(in_array(lcfirst($text), $possible_input) == false && strtolower($text) != "info"){
	//checking if current channel_id is already on database to either update or add
	$sql = "SELECT channel_id FROM players where channel_id = '" . $channel_id ."'"; 
	$checkChannel = sendQuery($sql, NULL);
	$channel_game_exists = false;
	if($checkChannel['channel_id'] != NULL){$channel_game_exists = true;}

	//will restart table on the row of channel_id to new game
	if($channel_game_exists){
		$sql = "UPDATE players 
				SET player1 = :player1,
				player2 = :player2,
				turn = :turn
				WHERE channel_id = :channel_id"; 
		$namedParameters = array('player1' => $user_name,
	    						 'player2' => $text,
	    						 'turn' => $user_name,
	    						 'channel_id' => $channel_id);
		sendQuery($sql, $namedParameters);

		$sql = "UPDATE grid
				SET a1 = NULL,
				a2 = NULL,
				a3 = NULL,
				b1 = NULL, 
				b2 = NULL,
				b3 = NULL,
				o1 = NULL,
				o2 = NULL,
				o3 = NULL
				WHERE channel_id = '" . $channel_id . "'";
		sendQuery($sql, NULL);
	}

	else{
		//insert a new row if channel_id not there for new game
	    $sql = "INSERT INTO players
	    		(player1, player2, turn, channel_id)
	    		VALUES (:player1, :player2, :turn, :channel_id)"; 
	    $namedParameters = array('player1' => $user_name,
	    						 'player2' => $text,
	    						 'turn' => $user_name,
	    						 'channel_id' => $channel_id);
	    sendQuery($sql, $namedParameters);

	    $sql = "INSERT INTO grid (channel_id) values('".$channel_id."')";
		sendQuery($sql, NULL);
    } 

    //introductory information for the game after first player initiates game 
    $finalMessage .= $user_name . " please start the game!\n";
    $finalMessage .= $user_name . " will be: :x:\n";
    $finalMessage .= $text . " will be: :o:\n";
    $finalMessage .= "The available spaces will be: :anger: \n";
    $finalMessage .= "Enter the following to display who's turn it is and the board: /tic_tac_toe info \n";
    $finalMessage .= "To make the first move please enter the row letter followed by the column number.\n";
    $finalMessage .= "i.e. /tic_tac_toe o3\n";
    $finalMessage .= displayBoard($channel_id);
}

//will display the person of who has a next turn and the current board
elseif (strtolower($text) == "info") {
		$finalMessage .= "The next turn belongs to " . checkNextTurn($channel_id) . "!\n";
		$finalMessage .= "The current board is as follows:\n" . displayBoard($channel_id);
}

//getting to else means user entered a possible space value on the grid
else{ 
	//There are no more available spaces or someone has one game already
	if(checkIfLineFormed($channel_id) || checkIfAllSpacesTaken($channel_id)){
	$finalMessage = "The game has ended! Please start a new game. i.e. /tic_tac_toe username";
	}

	//can continue game
	else{
		//check if it was the current user's turn
		$sql = "SELECT * FROM players where channel_id = '" . $channel_id ."'";
		$player = sendQuery($sql, NULL);
		if($player['turn'] == $user_name){
			//select space value entered to see if it has not alredy been entered
			$sql = "SELECT " . $text . " FROM grid WHERE channel_id = '" . $channel_id . "'";
			$grid = sendQuery($sql, NULL);
			if($grid[$text] == null){ //NULL means space is available on grid
				//will determine whether to put O or X on grid; 0 = O and 1 = X;
				$xo = 0;
				if($player['player1'] == $user_name){
					$xo = 1;
				}
				//update grid 
				$sql = "UPDATE grid
		    		SET ". $text ." = ". $xo . " 
		    		WHERE channel_id = '" . $channel_id . "'";
			    sendQuery($sql, NULL);

				//update turn for next game
				if($player['turn'] == $player['player1']) {$next_turn = $player['player2'];}
				else {$next_turn = $player['player1'];}
				$sql = "UPDATE players 
		    		SET turn = '".$next_turn."'
		    		WHERE channel_id = '" . $channel_id . "'";
			    sendQuery($sql, NULL);

			    //determines if current player won
			    if(checkIfLineFormed($channel_id)){
			    	$finalMessage .= "Game has ended! " . $user_name . " has won! \n";
			    }
			    //determines if all spaces are taken and tied concluded
			    elseif (checkIfAllSpacesTaken($channel_id)) {
			    	$finalMessage .= "All spaces have been taken now. Game has ended on a tie!\n";
			    }
			    else {
					$finalMessage .= checkNextTurn($channel_id) . " you are next!\n";
			    }
			}
			// the person's who's turn it was entered a value that was already entered
			else{
				$finalMessage = $text . " was already entered. Please enter a valid space value\n";
			}
			//add game board to display
			$finalMessage .= displayBoard($channel_id);
		}
		else{//indicate that user went out of turn
			$finalMessage .= "It is " . $player['turn'] . "'s turn. Please let " . $player['turn'] . " make the next move!";
		}
	}
}
//send message to Slack channel
sendMessage($finalMessage);
?>
