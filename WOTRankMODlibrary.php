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

function WN8Rating($rating)
{
    if ($rating < 299)
    {
        $name = "VERY BAD";
    } elseif (($rating >= 300) && ($rating <= 599))
    {
        $name = "BAD";
    } elseif (($rating >= 600) && ($rating <= 899))
    {
        $name = "BELOW AVERAGE";
    } elseif (($rating >= 900) && ($rating <= 1249))
    {
        $name = "AVERAGE";
    } elseif (($rating >= 1250) && ($rating <= 1599))
    {
        $name = "GOOD";
    } elseif (($rating <= 1600) && ($rating <= 1899))
    {
        $name = "VERY GOOD";
    } elseif (($rating >= 1900) && ($rating <= 2349))
    {
        $name = "GREAT";
    } elseif (($rating <= 2350) && ($rating <= 2899))
    {
        $name = "UNICUM";
    } elseif ($rating >= 2900)
    {
        $name = "SUPER UNICUM";
    }
    return $name;
}


function wn8($playerInfos, $playerTanks, $WNExpected)
{
    $playerId = array_keys($playerInfos["data"])[0];
    $playerTanks = $playerTanks["data"]["$playerId"];
    $playerInfos = $playerInfos["data"]["$playerId"];
    $WNExpectedArray = array();


    foreach ($WNExpected->data as $WNExpectedTank)
    {
        $WNExpectedArray[$WNExpectedTank->IDNum] = $WNExpectedTank;
    }


    $tanksCount = count($playerTanks);
    $avgDmg = $playerInfos["statistics"]["all"]["damage_dealt"];
    $avgSpot = $playerInfos["statistics"]["all"]["spotted"];
    $avgFrag = $playerInfos["statistics"]["all"]["frags"];
    $avgDef = $playerInfos["statistics"]["all"]["dropped_capture_points"];
    $avgWinRate = $playerInfos["statistics"]["all"]["wins"];
    $expDmg = $expSpot = $expFrag = $expDef = $expWins = 0;
    foreach ($playerTanks as $playerTank)
    {
        $id = $playerTank["tank_id"];
        $battles = $playerTank["statistics"]["battles"];
        $expDmg += $battles * $WNExpectedArray[$id]->expDamage;
        $expSpot += $battles * $WNExpectedArray[$id]->expSpot;
        $expFrag += $battles * $WNExpectedArray[$id]->expFrag;
        $expDef += $battles * $WNExpectedArray[$id]->expDef;
        $expWins += 0.01 * $battles * $WNExpectedArray[$id]->expWinRate;
    }
    $rDAMAGE = $playerInfos["statistics"]["all"]["damage_dealt"] / $expDmg;
    $rSPOT = $playerInfos["statistics"]["all"]["spotted"] / $expSpot;
    $rFRAG = $playerInfos["statistics"]["all"]["frags"] / $expFrag;
    $rDEF = $playerInfos["statistics"]["all"]["dropped_capture_points"] / $expDef;
    $rWIN = $playerInfos["statistics"]["all"]["wins"] / $expWins;
    $rWINc = max(0, ($rWIN - 0.71) / (1 - 0.71));
    $rDAMAGEc = max(0, ($rDAMAGE - 0.22) / (1 - 0.22));
    $rFRAGc = max(0, min($rDAMAGEc + 0.2, ($rFRAG - 0.12) / (1 - 0.12)));
    $rSPOTc = max(0, min($rDAMAGEc + 0.1, ($rSPOT - 0.38) / (1 - 0.38)));
    $rDEFc = max(0, min($rDAMAGEc + 0.1, ($rDEF - 0.10) / (1 - 0.10)));
    $WN8 = 980 * $rDAMAGEc + 210 * $rDAMAGEc * $rFRAGc + 155 * $rFRAGc * $rSPOTc +
        75 * $rDEFc * $rFRAGc + 145 * min(1.8, $rWINc);
    return round($WN8);
}

function setTsUserWN8RatingVERYBAD($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank1SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingBAD($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank2SG, ($WoTRAnk["TsDBID"]));

    }


}
function setTsUserWN8RatingBELOWAVERAGE($WoTRAnk, $ts3_VirtualServer, $rank1SG,
    $rank2SG, $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank3SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingAVERAGE($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank4SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingGOOD($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank5SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingVERYGOOD($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank6SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingGREAT($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank6SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank7SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingUNICUM($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank9SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank8SG, ($WoTRAnk["TsDBID"]));

    }


}

function setTsUserWN8RatingSUPERUNICUM($WoTRAnk, $ts3_VirtualServer, $rank1SG, $rank2SG,
    $rank3SG, $rank4SG, $rank5SG, $rank6SG, $rank7SG, $rank8SG, $rank9SG)
{

    if (in_array($rank1SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank1SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank2SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank2SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank3SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank3SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank4SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank4SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank5SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank5SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank6SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank7SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank7SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (in_array($rank8SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientDel($rank8SG, ($WoTRAnk["TsDBID"])), $ts3_VirtualServer->
            serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    } elseif (!in_array($rank9SG, explode(',', $WoTRAnk["TsSERVGIDs"])))
    {

        echo $ts3_VirtualServer->serverGroupClientAdd($rank9SG, ($WoTRAnk["TsDBID"]));

    }


}

?>
