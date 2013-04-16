<?php

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version alpha(2013-2-4)
 * @link https://github.com/jstar88/opbe
 */
define(PATH, ROOT_PATH . 'includes/libs/opbe/');
include (PATH . 'utils/includer.php');

define(INDEX_DEFENDER_SHIPS_NAME, 'def');
define(INDEX_ATTACKER_SHIPS_NAME, 'detail');
define(INDEX_DEFENDER_TECHS_NAME, 'techs');
define(INDEX_ATTACKER_TECHS_NAME, 'techs');
define(ID_MIN_SHIPS, 100);
define(ID_MAX_SHIPS, 300);
define(HOME_FLEET, 0);
define(INDEX_ATTACKERS_NAME, 'attackers');
define(INDEX_DEFENDERS_NAME, 'defenders');
define(INDEX_DEBRIS_ATTACKER_NAME, 'attacker');
define(INDEX_DEBRIS_DEFENDER_NAME, 'defender');
define(INDEX_ATTACKERS_AMOUNT_NAME, 'attackA');
define(INDEX_DEFENDERS_AMOUNT_NAME, 'defenseA');
define(INDEX_ATTACKERS_INFO, 'infoA');
define(INDEX_DEFENDERS_INFO, 'infoD');
define(DEFENDERS_WON, 'r');
define(ATTACKERS_WON, 'a');
define(DRAW, 'w');
define(METAL_ID, 901);
define(CRYSTAL_ID, 902);
define(INDEX_RETURN_WON, 'won');
define(INDEX_RETURN_DEBRIS, 'debris');
define(INDEX_RETURN_RW, 'rw');
define(INDEX_RETURN_UNITLOST, 'unitLost');
define(INDEX_RETURN_ATTACKER_NAME, 'att');
define(INDEX_RETURN_DEFENDER_NAME, 'def');
define(PRICELIST_VAR_NAME, 'pricelist');
define(COMBATCAPS_VAR_NAME, 'CombatCaps');
define(INDEX_RF_NAME, 'sd');
define(INDEX_COST_NAME, 'cost');
define(INDEX_POWER_NAME, 'attack');
define(INDEX_SHIELD_NAME, 'shield');
define(INDEX_ATTACKERS_POWER_NAME, 'attack');
define(INDEX_DEFENSES_POWER_NAME, 'defense');
define(INDEX_ATTACKERS_ASSORBED, 'attackShield');
define(INDEX_DEFENDERS_ASSORBED, 'defShield');


/**
 * calculateAttack()
 * Calculate the battle using OPBE
 * 
 * @param array &$attackers
 * @param array &$defenders
 * @param mixed $FleetTF
 * @param mixed $DefTF
 * @return array
 */
function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF)
{
    global $pricelist, $CombatCaps;

    /********** BUILDINGS MODELS **********/
    //attackers
    $attackerGroupObj = new PlayerGroup();
    foreach ($attackers as $fleetID => $attacker)
    {
        $player = $attacker['player'];
        $attackerPlayerObj = $attackerGroupObj->createPlayerIfNotExist($player['id'], $player['military_tech'], $player['shield_tech'], $player['defence_tech']);
        $attackerFleetObj = new Fleet($fleetID);
        foreach ($attacker['unit'] as $element => $amount)
        {
            $fighters = getFighters($element, $amount);
            $attackerFleetObj->add($fighters);
        }
        $attackerPlayerObj->addFleet($attackerFleetObj);
    }
    //defenders
    $defenderGroupObj = new PlayerGroup();
    foreach ($defenders as $fleetID => $defender)
    {
        $player = $attacker['player'];
        $defenderPlayerObj = $defenderGroupObj->createPlayerIfNotExist($player['id'], $player['military_tech'], $player['shield_tech'], $player['defence_tech']);
        $defenderFleetObj = getFleet($fleetID);
        foreach ($defender['unit'] as $element => $amount)
        {
            $fighters = getFighters($element, $amount);
            $defenderFleetObj->add($fighters);
        }
        $defenderPlayerObj->addFleet($defenderFleetObj);
    }

    /********** BATTLE ELABORATION **********/
    $opbe = new Battle($attackerGroupObj, $defenderGroupObj);
    $opbe->startBattle();
    $report = $opbe->getReport();

    /********** WHO WON **********/
    if ($report->defenderHasWin())
    {
        $won = DEFENDERS_WON;
    }
    elseif ($report->attackerHasWin())
    {
        $won = ATTACKERS_WON;
    }
    else
    {
        $won = DRAW;
    }

    /********** ROUNDS INFOS **********/

    $ROUND = array();
    $i = 1;
    for (; $i <= $report->getLastRoundNumber(); $i++)
    {
        $attackerGroupObj = $report->getPresentationAttackersFleetOnRound($i);
        $defenderGroupObj = $report->getPresentationDefendersFleetOnRound($i);
        $attackAmount = $attackerGroupObj->getTotalCount();
        $defenseAmount = $defenderGroupObj->getTotalCount();
        $attArray = updatePlayers($attackerGroupObj, $attackers, INDEX_ATTACKER_SHIPS_NAME, INDEX_ATTACKER_TECHS_NAME);
        $defArray = updatePlayers($defenderGroupObj, $defenders, INDEX_DEFENDER_SHIPS_NAME, INDEX_DEFENDER_TECHS_NAME);
        $ROUND[$i - 1] = roundInfo($report, $attackers, $defenders, $attackerGroupObj, $defenderGroupObj, $i, $attArray, $defArray);

    }
    //after battle
    $attackerGroupObj = $report->getAfterBattleAttackers();
    $defenderGroupObj = $report->getAfterBattleDefenders();
    $attArray = updatePlayers($attackerGroupObj, $attackers, INDEX_ATTACKER_SHIPS_NAME, INDEX_ATTACKER_TECHS_NAME);
    $defArray = updatePlayers($defenderGroupObj, $defenders, INDEX_DEFENDER_SHIPS_NAME, INDEX_DEFENDER_TECHS_NAME);
    $ROUND[$i - 1] = roundInfo($report, $attackers, $defenders, $attackerGroupObj, $defenderGroupObj, $i, $attArray, $defArray);

    /********** DEBRIS **********/
    //attackers
    $debAtt = $report->getAttackerDebris();
    $debAttMet = $debAtt[0];
    $debAttCry = $debAtt[1];
    //defenders
    $debDef = $report->getDefenderDebris();
    $debDefMet = $debDef[0];
    $debDefCry = $debDef[1];

    /********** LOST UNITS **********/
    $totalLost = array(INDEX_DEBRIS_ATTACKER_NAME => $report->getTotalAttackersLostUnits(), INDEX_DEBRIS_DEFENDER_NAME => $report->getTotalDefendersLostUnits());


    /********** RETURNS **********/
    return array(
        INDEX_RETURN_WON => $won,
        INDEX_RETURN_DEBRIS => array(INDEX_RETURN_ATTACKER_NAME => array(METAL_ID => $debAttMet, CRYSTAL_ID => $debAttCry), INDEX_RETURN_DEFENDER_NAME => array(METAL_ID => $debDefMet, CRYSTAL_ID => $debDefCry)),
        INDEX_RETURN_RW => $ROUND,
        INDEX_RETURN_UNITLOST => $totalLost);
}


/**
 * roundInfo()
 * Return the info required to fill $ROUND
 * @param BattleReport $report
 * @param array $attackers
 * @param array $defenders
 * @param PlayerGroup $attackerGroupObj
 * @param PlayerGroup $defenderGroupObj
 * @param int $i
 * @return array
 */
function roundInfo(BattleReport $report, $attackers, $defenders, PlayerGroup $attackerGroupObj, PlayerGroup $defenderGroupObj, $i, $attArray, $defArray)
{
    return array(
        INDEX_ATTACKERS_POWER_NAME => $report->getAttackersFirePower($i),
        INDEX_DEFENSES_POWER_NAME => $report->getDefendersFirePower($i),
        INDEX_DEFENDERS_ASSORBED => $report->getDefendersAssorbedDamage($i),
        INDEX_ATTACKERS_ASSORBED => $report->getAttachersAssorbedDamage($i),
        INDEX_ATTACKERS_NAME => $attackers,
        INDEX_DEFENDERS_NAME => $defenders,
        INDEX_ATTACKERS_AMOUNT_NAME => $attackerGroupObj->getTotalCount(),
        INDEX_DEFENDERS_AMOUNT_NAME => $defenderGroupObj->getTotalCount(),
        INDEX_ATTACKERS_INFO => $attArray,
        INDEX_DEFENDERS_INFO => $defArray);
}


/**
 * updatePlayers()
 * Update players array as default 2moons require
 * 
 * @param PlayerGroup $playerGroup
 * @param array &$players
 * @return null
 */
function updatePlayers(PlayerGroup $playerGroup, &$players, $indexShipsName, $indexTechsName)
{
    $plyArray = array();
    foreach ($playerGroup as $idPlayer => $player)
    {
        foreach ($player as $idFleet => $fleet)
        {
            $players[$idFleet][$indexTechsName] = array(
                $player->getWeaponsTech(),
                $player->getArmourTech(),
                $player->getShieldsTech());

            foreach ($fleet as $idFighters => $fighters)
            {
                $players[$idFleet][$indexShipsName][$idFighters] = $fighters->getCount();
                $plyArray[$idFleet][$idFighters] = array(
                    'def' => $fighters->getHull() * $fighters->getCount(),
                    'shield' => $fighters->getShield() * $fighters->getCount(),
                    'att' => $fighters->getPower() * $fighters->getCount());
            }
        }
    }
    return $plyArray;
}


/**
 * getFighters()
 * Choose the correct class type by ID
 * 
 * @param int $id
 * @param int $count
 * @return a Ship or Defense instance
 */
function getFighters($id, $count)
{
    $CombatCaps = $GLOBALS[COMBATCAPS_VAR_NAME];
    $pricelist = $GLOBALS[PRICELIST_VAR_NAME];
    $rf = $CombatCaps[$id][INDEX_RF_NAME];
    $shield = $CombatCaps[$id][INDEX_SHIELD_NAME];
    $cost = array($pricelist[$element][INDEX_COST_NAME][METAL_ID], $pricelist[$element][INDEX_COST_NAME][CRYSTAL_ID]);
    $power = $CombatCaps[$id][INDEX_POWER_NAME];
    if ($id > ID_MIN_SHIPS && $id < ID_MAX_SHIPS)
    {
        return new Ship($id, $count, $rf, $shield, $cost, $power);
    }
    return new Defense($id, $count, $rf, $shield, $cost, $power);
}


/**
 * getFleet()
 * Choose the correct class type by ID
 * 
 * @param int $id
 * @return a Fleet or HomeFleet instance
 */
function getFleet($id)
{
    if ($id == HOME_FLEET)
    {
        return new HomeFleet(HOME_FLEET);
    }
    return new Fleet($id);
}

?>