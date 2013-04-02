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
