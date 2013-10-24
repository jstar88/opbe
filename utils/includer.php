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
require (OPBEPATH."utils/DeepClonable.php");
require (OPBEPATH."utils/Math.php");
require (OPBEPATH."utils/Number.php");
require (OPBEPATH."utils/Events.php");
require (OPBEPATH."models/Type.php");
require (OPBEPATH."models/Fighters.php");
require (OPBEPATH."models/Fleet.php");
require (OPBEPATH."models/HomeFleet.php");
include (OPBEPATH."models/Defense.php");
include (OPBEPATH."models/Ship.php");
require (OPBEPATH."models/Player.php");
require (OPBEPATH."models/PlayerGroup.php");
require (OPBEPATH."combatObject/Fire.php");
require (OPBEPATH."combatObject/PhysicShot.php");
require (OPBEPATH."combatObject/ShipsCleaner.php");
require (OPBEPATH."combatObject/FireManager.php");
require (OPBEPATH."core/Battle.php");
require (OPBEPATH."core/BattleReport.php");
require (OPBEPATH."core/Round.php");
require (OPBEPATH."constants/battle_constants.php");
?>
