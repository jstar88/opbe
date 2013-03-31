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
define('ROUNDS', 6);

define('SHIELDS_TECH_INCREMENT_FACTOR', 0.1);
define('ARMOUR_TECH_INCREMENT_FACTOR', 0.1);
define('WEAPONS_TECH_INCREMENT_FACTOR', 0.1);

define('MIN_PROB_TO_EXPLODE', 0.3);
define('SHIELD_CELLS', 100);

define('DEBRIS_FACTOR', 0.3);
define('MOON_UNIT_PROB', 100000);
define('MAX_MOON_PROB', 20);

define('COST_TO_ARMOUR', 0.1);
define('POINT_UNIT', 1000);

define('BATTLE_WIN', 1);
define('BATTLE_LOSE', -1);
define('BATTLE_DRAW', 0);

define('USE_SERIALIZATION_TO_CLONE',true);
define('USE_PARTIAL_SERIALIZATION_TO_CLONE',false);

define('MOON_MIN_START_SIZE',2000);
define('MOON_MAX_START_SIZE',6000);
define('MOON_MIN_FACTOR',100);
define('MOON_MAX_FACTOR',200);

define('DEFENSE_REPAIR_PROB',0.7);
define('SHIP_REPAIR_PROB',0);

define('ONLY_FIRST_AND_LAST_ROUND',false);
