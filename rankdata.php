<?php
/*
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 */


	$user = ''; // default
	$plat = 'steam'; // default

	$rankData = array(
				'message' => 'Success',
				'code' => '1',
		
		
				'name' => '',
				'1v1' => array(0, 100),
				'2v2' => array(0, 100),
				'3v3' => array(0, 100),
				'Solo 3v3' => array(0, 100),
				'Hoops' => array(0, 100),
				'Rumble' => array(0, 100),
				'Dropshot' => array(0, 100),
				'Snowday' => array(0, 100),

				'Wins' => '0',
				'Goals' => '0',
				'Saves' => '0',
				'Assists' => '0',
				'Shots' => '0',
				'MVPs' => '0',
				'GoalShotRatio' => '0'
	);

	if (!empty($_GET['user'])) { // is user parameter given 
		$user = str_replace(array(' ', '%20'), array('-', '-') , strtolower($_GET['user'])); // set user the value of given parameter in lower case and replace spaces with hyphen 
	}
	if (!empty($_GET['plat'])) { // is plat parameter given
		$plat = strtolower($_GET['plat']); // set plat the value of given parameter in lower case
	}


	// valid platforms
	$platforms = array('ps'=>1, 'steam'=>1, 'xbox'=>1); 


	// no username or too long username
	if (($user=='') || (strlen($user)>32)) {
		$rankData['message'] = 'No/Invalid username given.';
		$rankData['code'] = '0';
		die(json_encode($rankData));
	}

	// validate platform
	if (!isset($platforms[$plat])) {
		$rankData['message'] = $plat.' is an invalid platform on rocketleague.tracker.network';
		$rankData['code'] = '0';
		die(json_encode($rankData));
	}



	$RL_tracker = @file_get_contents('https://rocketleague.tracker.network/profile/'.$plat.'/'.$user); // get html code

	preg_match_all("#addPlayer\(.+?, '(.+?)',.+?\)#is", $RL_tracker, $name); // get player name in javascript code
	preg_match_all("/playlist-tracking'\)\.highcharts\((.+?)\)\;/is", $RL_tracker, $first); // first = rank numbers and playlistnames in javascript code
	preg_match_all("/playlist-tracking-rating'\)\.highcharts\((.+?)\)\;/is", $RL_tracker, $second); // second = playlistnames and points in javascript code


	if (count($name[0])==0) { // checking for a existing username (if not, no ranks given)
		$rankData['message'] = $user.' ('.$plat.') was not found on rocketleague.tracker.network';
		$rankData['code'] = '0';
		die(json_encode($rankData));
	}

	$name = html_entity_decode($name[1][0], ENT_QUOTES | ENT_XML1, 'UTF-8'); // set name to found name


	preg_match_all("/name: '(.+?)'/", $first[1][0], $playlists); // get playlist names
	preg_match_all("/data: (\[.*?\])/", $first[1][0], $rankLevel); // get data(rank) of playlist names
	preg_match_all("/<div class=\"value\" data-stat=\"(Wins|Goals|Saves|Assists|Shots|MVPs|GoalShotRatio)\">\s*(.+?)\s*<\/div>/is", $RL_tracker, $stats); // get stats in html code


	if (count($rankLevel[0])==0) { // check if ranks exists
		$rankData['message'] = $name.' ('.$plat.') has no ranks yet.';
		$rankData['code'] = '0';
		die(json_encode($rankData));
	}


	$playlistNames = array('Ranked Duel 1v1'=>'1v1', 'Ranked Doubles 2v2'=>'2v2', 'Ranked Solo Standard 3v3'=>'Solo 3v3', 'Ranked Standard 3v3'=>'3v3', 'Hoops' => 'Hoops', 'Rumble' => 'Rumble', 'Dropshot' => 'Dropshot', 'Snowday' => 'Snowday'); // array of short formatted playlist names

	$rankNames = array('Unranked', 'Bronze I', 'Bronze II', 'Bronze III', 'Silver I', 'Silver II', 'Silver III', 'Gold I', 'Gold II', 'Gold III', 'Platinum I', 'Platinum II', 'Platinum III', 'Diamond I', 'Diamond II', 'Diamond III', 'Champion I', 'Champion II', 'Champion III', 'Grand Champion'); // array of all possible rank names (bottom up)

	
	$rankData['name'] = $name; // update data in array

	if(count($stats)>0) { // iterate through all given stats
		for($i=0; $i < count($stats[1]); $i++){
			$rankData[$stats[1][$i]] = $stats[2][$i]; // update data in array
		}
	}

	for($i=0; $i < count($rankLevel[1]); $i++) { // iterate through all given playlists 

		$jsonArray = json_decode($rankLevel[1][$i]); // format javascript array to php array
		$rankNumber = array_pop($jsonArray); // get last element

		preg_match_all("/name: '".$playlists[1][$i]."', data: (\[.+?\])/", $second[1][0], $rankMMR); // get MMR history of current playlist

		if(count($rankMMR[1])!=0) { // if has history
			$jsonMMR = json_decode($rankMMR[1][0]); // format javascript array to php array 
			$mmr = array_pop($jsonMMR); // get last element (last MMR in history)
			$rankData[$playlistNames[$playlists[1][$i]]] = array($rankNumber, $mmr); // update data in array
		}
	}

	echo json_encode($rankData);


?>
