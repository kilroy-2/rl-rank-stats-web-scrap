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
				'SeasonReward' => array(0, 0), // Level, Wins
				'1v1' => array(0, 100), // rankNumber, MMR
				'2v2' => array(0, 100), // rankNumber, MMR
				'3v3' => array(0, 100), // rankNumber, MMR
				'Solo 3v3' => array(0, 100), // rankNumber, MMR
				'Hoops' => array(0, 100), // rankNumber, MMR
				'Rumble' => array(0, 100), // rankNumber, MMR
				'Dropshot' => array(0, 100), // rankNumber, MMR
				'Snowday' => array(0, 100), // rankNumber, MMR
				'Tournament' => array(0, 100), // rankNumber, MMR

				'Wins' => 0,
				'Goals' => 0,
				'Saves' => 0,
				'Assists' => 0,
				'Shots' => 0,
				'MVPs' => 0,
				'GoalShotRatio' => 0.0
	);
	
	function hasPlaylist($name, $data){
		for ($i = 0 ; $i < count($data) ; $i++){
			if (isset($data[$i]['metadata'])){
				if ($data[$i]['metadata']['name'] == $name){
					return $i;
				}
			}
		}
		return -1;
	}

	if (!empty($_GET['user'])) { // is user parameter given 
		$user = str_replace(array(' ', '%20'), array('-', '-') , strtolower($_GET['user'])); // set user the value of given parameter in lower case and replace spaces with hyphen 
	}
	if (!empty($_GET['plat'])) { // is plat parameter given
		$plat = strtolower($_GET['plat']); // set plat the value of given parameter in lower case
	}


	// valid platforms
	$platforms = array('psn'=>1, 'steam'=>1, 'xbox'=>1); 


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



	$RL_tracker = @file_get_contents('https://rocketleague.tracker.network/rocket-league/profile/'.$plat.'/'.$user.'/overview'); // get html code

	preg_match_all("/\"segments\":(.+?),\"availableSegments\"/is", $RL_tracker, $first); // first = json of stats and rank data
	preg_match_all("/\"platformUserHandle\":\"(.+?)\",\"platformUserIdentifier\"/is", $RL_tracker, $name); // fetch name

	$rankData['name'] = isset($name[1][0]) ? html_entity_decode($name[1][0], ENT_QUOTES | ENT_XML1, 'UTF-8') : $user; // set name


	if (count($first[0])==0) { // checking for existing data (if not, no ranks given)
		$rankData['message'] = $user.' ('.$plat.') has no data on rocketleague.tracker.network yet.';
		$rankData['code'] = '0';
		die(json_encode($rankData));
	}

	$data = json_decode($first[1][0], true); // decode to php array 
	// or use the array $data instead of $rankData, ofc it has lots more info.
	// var_dump($data); 

	$rankData['SeasonReward'] = array($data[0]['stats']['seasonRewardLevel']['value'], $data[0]['stats']['seasonRewardWins']['value']);
	$rankData['Wins'] = $data[0]['stats']['wins']['value'];
	$rankData['Goals'] = $data[0]['stats']['goals']['value'];
	$rankData['MVPs'] = $data[0]['stats']['mVPs']['value'];
	$rankData['Saves'] = $data[0]['stats']['saves']['value'];
	$rankData['Assists'] = $data[0]['stats']['assists']['value'];
	$rankData['Shots'] = $data[0]['stats']['shots']['value'];
	$rankData['GoalShotRatio'] = $data[0]['stats']['goalShotRatio']['value'];

	$playlistNames = array('Ranked Duel 1v1'=>'1v1', 'Ranked Doubles 2v2'=>'2v2', 'Ranked Solo Standard 3v3'=>'Solo 3v3', 'Ranked Standard 3v3'=>'3v3', 'Hoops' => 'Hoops', 'Rumble' => 'Rumble', 'Dropshot' => 'Dropshot', 'Snowday' => 'Snowday', 'Tournament Matches' => 'Tournament'); // array of short formatted playlist names

	if(count($data) > 1 ) { // not sure if consistent yet, maybe needs some revamp
		foreach ($playlistNames as $key => $value) {
			$id = hasPlaylist($key, $data);

			if($id != -1){
				$rankData[$value] = array($data[$id]['stats']['tier']['value'], $data[$id]['stats']['rating']['value']); // rankNumber, MMR
			}
		}
	}

		
	
	$rewardLevels = array('Unranked', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Champion', 'Grand Champion', 'Supersonic Legend'); // array of all possible reward levels (bottom up)

	$rankNames = array('Unranked', 'Bronze I', 'Bronze II', 'Bronze III', 'Silver I', 'Silver II', 'Silver III', 'Gold I', 'Gold II', 'Gold III', 'Platinum I', 'Platinum II', 'Platinum III', 'Diamond I', 'Diamond II', 'Diamond III', 'Champion I', 'Champion II', 'Champion III', 'Grand Champion I', 'Grand Champion II', 'Grand Champion III', 'Supersonic Legend'); // array of all possible rank names (bottom up)


	echo json_encode($rankData);


?>
