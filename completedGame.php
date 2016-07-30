<?php  

require 'databaseConnection.php';

function checkIfLineFormed($channel_id){
		$sql = "SELECT * FROM grid WHERE channel_id = '" . $channel_id . "'";
		$grid = sendQuery($sql, NULL);

		//line formed from row a
		if($grid['a1'] != NULL && $grid['a1'] == $grid['a2'] && $grid['a2'] == $grid['a3']){
			return true;
		}
		//line formed from row b
		elseif($grid['b1'] != NULL && $grid['b1'] == $grid['b2'] && $grid['b2'] == $grid['b3']){
			return true;
		}
		//line formed from row o
		elseif($grid['o1'] != NULL && $grid['o1'] == $grid['o2'] && $grid['o2'] == $grid['o3']){
			return true;
		}
		//line formed from column 1
		elseif($grid['a1'] != NULL && $grid['a1'] == $grid['b1'] && $grid['b1'] == $grid['o1']){
			return true;
		}
		//line formed from column 2
		elseif($grid['a2'] != NULL && $grid['a2'] == $grid['b2'] && $grid['b2'] == $grid['o2']){
			return true;
		}
		//line formed from column 3
		elseif($grid['a3'] != NULL && $grid['a3'] == $grid['b3'] && $grid['b3'] == $grid['o3']){
			return true;
		}
		//diagonal line formed from a1 to o3
		elseif($grid['a1'] != NULL && $grid['a1'] == $grid['b2'] && $grid['b2'] == $grid['o3']){
			return true;
		}
		//diagonal line formed from o1 to a3
		elseif($grid['a3'] != NULL && $grid['a3'] == $grid['b2'] && $grid['b2'] == $grid['o1']){
			return true;
		}
		return false;
}

function checkIfAllSpacesTaken($channel_id){
		$sql = "SELECT * FROM grid where channel_id = '" . $channel_id . "'";
		$grid = sendQuery($sql, NULL);
		//will check if none of the spaces left are equal to NULL
		if($grid['a1'] != NULL && $grid['a2'] != NULL && $grid['a3'] != NULL
			&& $grid['b1'] != NULL && $grid['b2'] != NULL && $grid['b3'] != NULL
			&& $grid['o1'] != NULL && $grid['o2'] != NULL && $grid['o3'] != NULL){
			return true;
		}
		return false;
}

?>