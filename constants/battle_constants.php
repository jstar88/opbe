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
 * @version beta(26-10-2013)
 * @link https://github.com/jstar88/opbe
 */

/* System constants, do not edit! */
define('BATTLE_WIN', 1);
define('BATTLE_LOSE', -1);
define('BATTLE_DRAW', 0);
define('SHIELD_CELLS', 100); //how many cells a ship's shield should contain. Carefull to edit: more cells = better accuracy but less bounces in some cases.
define('USE_SERIALIZATION_TO_CLONE', true); //Because PHP default clone function is bugged, we provide differents ways.
define('USE_PARTIAL_SERIALIZATION_TO_CLONE', false);
define('PROB_TO_REAL_MAGIC',2); //value used to adapt probability theory to critical cases.
define('USE_HITSHIP_LIMITATION',false);
define('USE_BIEXPLOSION_SYSTEM',true);

/* Battle constants, default as Ogame */
define('ROUNDS', 6); //how many rounds a battle have, no limits.
define('SHIELDS_TECH_INCREMENT_FACTOR', 0.1); //how much a level increase the shield, in percentage from 0 to 1.
define('ARMOUR_TECH_INCREMENT_FACTOR', 0.1); //how much a level increase the armour, in percentage from 0 to 1.
define('WEAPONS_TECH_INCREMENT_FACTOR', 0.1); //how much a level increase the weapon, in percentage from 0 to 1.
define('COST_TO_ARMOUR', 0.1); //how much cost equal the armour, from 0 to 1. 1 means the ships/defenses armour equal its cost.
define('MIN_PROB_TO_EXPLODE', 0.3); //minimum probability at one the ships/defenses can explode, from 0 to 1. 1 means that the ship/def can explode only when they lost all hp. 
define('DEFENSE_REPAIR_PROB', 0.7); //probability to repair defenses. From 0 to 1, 1 means that defenses are always rebuilt.
define('SHIP_REPAIR_PROB', 0); //same as below but for ships.

/* Views and optimization options */
define('ONLY_FIRST_AND_LAST_ROUND', false); //This option is usefull to decrease RAM usage, but the battle report will not contain all rounds.

/* After-battle constants, default as Ogame */
define('DEBRIS_FACTOR', 0.3); //Percentage of debris generated from destroyed resources.
define('POINT_UNIT', 1000); //Ogame point = 1000 resources.
define('MOON_UNIT_PROB', 100000);
define('MAX_MOON_PROB', 20); //max probability to moon creation. 
define('MOON_MIN_START_SIZE', 2000);
define('MOON_MAX_START_SIZE', 6000);
define('MOON_MIN_FACTOR', 100);
define('MOON_MAX_FACTOR', 200);
define('MOON_MAX_HIGHT_TEMP_DIFFERENCE_FROM_PLANET', 30);
define('MOON_MAX_LOW_TEMP_DIFFERENCE_FROM_PLANET', 10);
define('DEFAULT_MOON_NAME', 'moon');
