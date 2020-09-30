# rl-rank-stats-web-scrap
[![Open Source Love svg2](https://badges.frapsoft.com/os/v2/open-source.svg?v=103)](https://github.com/kilroy-2/rl-rank-stats-web-scrap) [![Ask Me Anything !](https://img.shields.io/badge/WRITTEN%20IN-PHP-787CB5.svg)](https://github.com/kilroy-2/rl-rank-stats-web-scrap)  

Script that scraps html code from https://rocketleague.tracker.network/  
to get rank and stats data of a player in Rocket League. Output is json.

You may need to ask for permission on rocketleague.tracker.network to scrap their site.

# Usage
```rankdata.php?user=USERID&platform=PLATFORM```  

Simply change **USERID** into your Steam profiles id, psn name, or xbox name, and **PLATFORM** into either steam, psn or xbox.  
Use the hyphen character for spaces.  
  
Output:  
`
{"message":"Success","code":"1","name":"guy","SeasonReward":[4,0],"1v1":[18,1106],"2v2":[18,1477],"3v3":[16,1268],"Solo 3v3":[0,1213],"Hoops":[15,950],"Rumble":[17,1080],"Dropshot":[0,976],"Snowday":[0,981],"Wins":17362,"Goals":38898,"Saves":40845,"Assists":16937,"Shots":89942,"MVPs":7263,"GoalShotRatio":43.247870850103}
`

# More
This script only outputs general and selected information that I find to be important. Of course theres way more that can be
in the output. If you want more, ```var_dump($data)``` and see the glorious amount of information you can access.  
Of course this can be used as an API running on your own server.
