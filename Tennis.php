<?php 

/*Tennis.php file created by Anupam Khosla*/

function computeGameState(string $nameP1, string $nameP2, array $wins) {

	//check for input first. If wrong input then throw error.
	//array can only have nameP1 or nameP2
	foreach($wins as $value) {
		if( ($value != $nameP1) && ($value != $nameP2) ) {
			die("Wrong input parameter $wins. It must have values which are either player1's name or player2's name");
		}
	}

	$scoreP1 = 0; //initial score 
	$scoreP2 = 0;
	$state = NULL; //state to be calculated after

	function score_incrm($nameP, $nameP1, $nameP2, &$scoreP1, &$scoreP2){ //every call will set score
		if($nameP == $nameP1) {
			switch(true) {
				case ($scoreP1 == 0):  
					$scoreP1 = 15;
					break;	
				case ($scoreP1 == 15):
					$scoreP1 = 30;
					break;
				case ($scoreP1 > 15):  
					$scoreP1 = $scoreP1 + 10;
					break;
			}
		} 
		else if($nameP == $nameP2) {			
			switch(true) {
				case ($scoreP2 == 0):  
					$scoreP2 = 15;
					break;				
				case ($scoreP2 == 15):
					$scoreP2 = 30;
					break;							
				case ($scoreP2 > 15):  
					$scoreP2 = $scoreP2 + 10;
					break;
			}			
		}
	}

	foreach($wins as $value) {
		//get total score for both players in $scoreP1 and $scoreP2
		score_incrm($value, $nameP1, $nameP2, $scoreP1, $scoreP2);	
	}


	if(max($scoreP1, $scoreP2) > 40) { //Someone crossed 40; we have a proper state
		if($scoreP1 == $scoreP2) {
			//Tie
			$state = "DEUCE";
		} 	
		else if( ($scoreP1 > $scoreP2) && ($scoreP1 - $scoreP2 > 10) ) {
			//P1 wins  
			$state = $nameP1." WINS";
		}	
		else if( ($scoreP1 > $scoreP2) && ($scoreP1 - $scoreP2 <= 10) ) {
			//P1 has advantag  
			$state = $nameP1." ADVANTAGE";
		}	
		else if( ($scoreP2 > $scoreP1) && ($scoreP2 - $scoreP1 > 10) ) {
			//P2 wins  
			$state = $nameP2." WINS";
		}		
		else if( ($scoreP2 > $scoreP1) && ($scoreP2 - $scoreP1 <= 10) ) {
			//P2 has advantag  
			$state = $nameP2." ADVANTAGE";
		}		
	}

	if( max($scoreP1, $scoreP2) <= 40 ) { //if no one crosses 40 then show normal score
		//no one got to 40, so lets see if theres a draw  
		if($scoreP1 == $scoreP2) {
			$state = $scoreP1."a";
		}
		else {
			$state = $nameP1." ".$scoreP1." - ".$nameP2." ".$scoreP2;
		}
	}
	return $state;
}


/*Tests*/
//You can change the $wins array to test if the output matches the expected result.
echo "Player1: Bob,   Player2 : Anna" . "<br>";  
echo computeGameState("Bob", "Anna",  ["Bob", "Bob", "Bob", ]);
// input for $wins like ["Bob", "Bob", "Bob", "Bob", "Anna", "Anna", "Anna", "Anna", "Anna"] are not considered
// as Bob would have won and the game would not continue. But the output we'll get is `Anna Advantage`
?>