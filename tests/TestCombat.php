<?php
/**
 * 
 * 
 * @package   
 * @author Jstar
 * @copyright Jstar
 * @version 2013
 * @access public
 */
include ("..\\utils\\DeepClonable.php");
include ("..\\models\\Type.php");
include ("..\\models\\Fighters.php");
include ("..\\models\\Fleet.php");
include ("..\\models\\HomeFleet.php");
include ("..\\models\\Player.php");
include ("..\\models\\Defense.php");
include ("..\\models\\Ship.php");
include ("..\\models\\PlayerGroup.php");
include ("..\\combatObject\\Fire.php");
include ("..\\combatObject\\PhysicShot.php");
include ("..\\combatObject\\ShipsCleaner.php");
include ("..\\combatObject\\FireManager.php");
include ("..\\core\\Battle.php");
include ("..\\core\\BattleReport.php");
include ("..\\core\\Round.php");
require ("..\\constants\\vars.php");
require ("..\\constants\\battle_constants.php");
include ("..\\utils\\Math.php");
include ("..\\utils\\Number.php");
include ("..\\utils\\Events.php");
function getFighters($id, $count)
{
    global $CombatCaps, $pricelist;
    $rf = $CombatCaps[$id]['sd'];
    $shield = $CombatCaps[$id]['shield'];
    $cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);
    $power = $CombatCaps[$id]['attack'];
    if ($id <= 217)
    {
        return new Ship($id, $count, $rf, $shield, $cost, $power);
    }
    return new Defense($id, $count, $rf, $shield, $cost, $power);
}

//------------Attackers--------------//

//battle_ship
$id = 204;
$count = 20;
$a2 = getFighters($id, $count);

//------------Defenders--------------//
//light_fighter
$id = 205;
$count = 10;
$d3 = getFighters($id, $count);

$attackers = new Fleet(1, array($a2));
$defenders = new Fleet(2, array($d3));

$playerA = new Player(1, array($attackers), 0, 0, 0);
$playerB = new Player(2, array($defenders), 0, 0, 0);

$groupA = new PlayerGroup(array($playerA));
$groupB = new PlayerGroup(array($playerB));

$memory1 = memory_get_usage();
$micro1 = microtime();

$engine = new Battle($groupA, $groupB);
$engine->startBattle(false);

$micro1 = microtime() - $micro1;
$memory1 = memory_get_usage() - $memory1;

$info = $engine->getReport();
echo $info->toString($resource);

$micro1 = round(1000 * $micro1, 2);
$memory1 = round($memory1 / 1000);
echo <<< EOT
<br>______________________________________________<br>
Battle calculated in <font color=blue>$micro1 ms</font>.<br>
Memory used: <font color=blue>$memory1 KB</font><br>
_______________________________________________<br>
EOT;
