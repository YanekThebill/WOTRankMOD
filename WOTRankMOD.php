/**
 * WOTRankMOD v.0.1 alfa
 * 
 * @author YanekThebill
 * @Copyright (C) 2014  <yanekthebill@gmail.com>
 * 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 3 of
 * the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * See <http://www.gnu.org/licenses/>.
 * 
 * 
 **/

//-------------------------------------------------------------------------------------------------------------
// TS  CONFIGURATION
//-------------------------------------------------------------------------------------------------------------
$APPID = ""; //Aplication ID
$botname = "WARGAMING"; //Bot Nickname;
$TSaddress = "127.0.0.1"; //TS server address
$TSqueryport = "10011"; //TS Query port (deflaut is: 10011)
$TSport = "9987"; //TS Server Port (deflaut is 9987)
$TSadminNickname = "serveradmin"; //TS  Admin Query nickname
$TSpassword = "pass"; // TS Admin Query password
$WotsgID = "15"; //WOT Server group Id
//------------------------------------------------------------------------------------------------------------------
// WOT WN8 RANK SG
$rank1SG = "104";
$rank2SG = "105";
$rank3SG = "106";
$rank4SG = "107";
$rank5SG = "108";
$rank6SG = "109";
$rank7SG = "110";
$rank8SG = "111";
$rank9SG = "112";
//-------------------------------------------------------------------------------------------------------------------
// ERROR REPORT
ini_set('display_errors', 'On');
error_reporting(E_ALL);

set_time_limit(60);

require_once dirname(__file__) .
    "/../WOTRANKMOD/ts3php/libraries/TeamSpeak3/TeamSpeak3.php";
require_once dirname(__file__) . "/../WOTRANKMOD/WOTRankMODlibrary.php";


try
{
    $ts3_VirtualServer = TeamSpeak3::factory("serverquery://$TSadminNickname:$TSpassword@$TSaddress:$TSqueryport/?server_port=$TSport&nickname=$botname");


}
catch (TeamSpeak3_Exception $e)
{
    // print the error message returned by the server
    echo "Error " . $e->getCode() . ": " . $e->getMessage();
}

$arr_WOTClientList = $ts3_VirtualServer->serverGroupGetById("$WotsgID");
$nicknamelist = "";
$WOTplayers = array();
$TSuserdata = array();
$WOTplayerStats = array();

foreach ($arr_WOTClientList as $ts3_Client)
{
    $nickname = $ts3_Client;
    // server admin client remove it
    if (strpos($nickname, 'serveradmin ') !== false)
    {
    } else
    {
        $nickname = preg_replace("/\([^)]+\)/", "", $nickname);
        $nickname = preg_replace('/\[[^\]]+\]/', '', $nickname);
        $nickname = preg_replace('/\s+/', '_', $nickname);
        $nickname = trim($nickname);
        $nicknamelist = $nicknamelist . $nickname . ",";
    }
}

foreach ($arr_WOTClientList as $TSClid => $TSArray)
{
    array_push($TSuserdata, array(

        "TsNICK" => $TSArray->client_nickname->toString(),
        "TsDBID" => $TSArray->client_database_id,
        "TsUID" => $TSArray->client_unique_identifier->toString(),
        "TsSERVGIDs" => $TSArray->client_servergroups,
        "TsCHANGIDs" => $TSArray->client_channel_group_id,
        ));
}

if (empty($TSuserdata))
{
    echo "No online users with WOT server group ";
    //$ts3_VirtualServer->message("No online users with WOT server group");
    die;
}
//$Summoners = array();
$Summoners = explode(",", $nicknamelist);

$i = 0;
foreach ($TSuserdata as $sumID)
{
    $sumID1 = $sumID["TsNICK"];
    $url1 = "http://api.worldoftanks.eu/wot/account/list/?application_id=$APPID&language=pl&type=exact&search=$sumID1";
    $JSON1 = file_get_contents($url1);
    $sumData[$i] = json_decode($JSON1, true);
    $i = $i + 1;
}

foreach ($sumData as $wotDATAarr)
{
    if ($wotDATAarr["status"] == "ok")
    {
        if (!empty($wotDATAarr["data"]))
        {
            array_push($WOTplayers, array( //"status" =>$wotDATAarr["status"],

                "nickname" => $wotDATAarr["data"]["0"]["nickname"], "account_id" => $wotDATAarr["data"]["0"]["account_id"]));
        } else
        {
            $ts3_VirtualServer->serverGroupClientDel($WotsgID, ($sumID["TsDBID"]));
            $ts3_VirtualServer->clientGetByDbid($sumID["TsDBID"])->message("\nNick, którego używasz nie został znaleziony w bazie [b]wargaming.net[/b]\n Zostajesz usunięty z grupy serwera WOT");
        }
    }

}


foreach ($WOTplayers as $key => $s1)
{

    foreach ($TSuserdata as $tr)
    {
        $s1["nickname"] = str_replace(' ', '', $s1["nickname"]);
        $tr["TsNICK"] = str_replace(' ', '', $tr["TsNICK"]);
        if (strtolower($tr["TsNICK"]) == strtolower($s1["nickname"]))
        {
            foreach ($tr as $k => $c)
            {
                $WOTplayers[$key][$k] = $c;
            }
            $find = 1;
            break;
        }
    }
}

foreach ($WOTplayers as $WOTplayersID)
{
    $sumID1 = $WOTplayersID["account_id"];
    $url = "http://www.wnefficiency.net/exp/expected_tank_values_16.json";
    $JSON = file_get_contents($url);
    $WNExpected = json_decode($JSON);


    $url1 = "http://api.worldoftanks.eu/wot/account/info/?application_id=$APPID&account_id=$sumID1";
    $JSON1 = file_get_contents($url1);
    $playerInfos = json_decode($JSON1, true);


    $url2 = "http://api.worldoftanks.eu/wot/account/tanks/?application_id=$APPID&account_id=$sumID1";
    $JSON2 = file_get_contents($url2);
    $playerTanks = json_decode($JSON2, true);

    if (!$playerInfos["data"]["$sumID1"]["statistics"]["all"]["xp"] == 0)
    {
        $rating = wn8($playerInfos, $playerTanks, $WNExpected);
    }
    if (isset($rating))
    {
        $WN8Name = WN8Rating($rating);

        array_push($WOTplayerStats, array(
            "account_id" => $WOTplayersID["account_id"],
            "WN8" => $rating,
            "WN8 Name" => $WN8Name));
    }

    foreach ($WOTplayers as $key => $s1)
    {

        foreach ($WOTplayerStats as $tr)
        {

            if ($tr["account_id"] == $s1["account_id"])
            {
                foreach ($tr as $k => $c)
                {
                    $WOTplayers[$key][$k] = $c;
                }
                $find = 1;
                break;
            }
        }
    }
}
var_dump($WOTplayers);

foreach ($WOTplayers as $WoTRAnk)
{
    if (isset($WoTRAnk["WN8 Name"]))
    {
        if ($WoTRAnk["WN8 Name"] === "VERY BAD")
        {
            if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in VERY BAD WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: VERY BAD "), setTsUserWN8RatingVERYBAD($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "BAD")
        {
            if (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in BAD WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: BAD"), setTsUserWN8RatingBAD($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "BELOW AVERAGE")
        {
            if (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in BELOW AVERAGE WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: BELOW AVERAGE"), setTsUserWN8RatingBELOWAVERAGE($WoTRAnk,
                    $ts3_VirtualServer, $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG,
                    $rank7SG, $rank8SG, $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "AVERAGE")
        {
            if (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in AVERAGE WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: AVERAGE"), setTsUserWN8RatingAVERAGE($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "GOOD")
        {
            if (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in GOOD WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: GOOD"), setTsUserWN8RatingGOOD($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "VERY GOOD")
        {
            if (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in VERY GOOD WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: VERY GOOD"), setTsUserWN8RatingVERYGOOD($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "GREAT")
        {
            if (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in GREAT WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: GREAT"), setTsUserWN8RatingGREAT($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "UNICUM")
        {
            if (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in UNICUM WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: UNICUM"), setTsUserWN8RatingUNICUM($WoTRAnk, $ts3_VirtualServer,
                    $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG,
                    $rank9SG);
            }
        } elseif ($WoTRAnk["WN8 Name"] === "SUPER UNICUM")
        {
            if (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
            {
                echo " <strong>" . $WoTRAnk["TsNICK"] .
                    "</strong> Still in SUPER UNICUM WN8 Rating. No action<br />";
            } else
            {
                echo $ts3_VirtualServer->clientGetByDbid($WoTRAnk["TsDBID"])->message("Twój wynik WN8 to : " .
                    $WoTRAnk["WN8"] . " Ocena: SUPER UNICUM"), setTsUserWN8RatingSUPERUNICUM($WoTRAnk,
                    $ts3_VirtualServer, $rank1SG, $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG,
                    $rank7SG, $rank8SG, $rank9SG);
            }
        }
    }
}
