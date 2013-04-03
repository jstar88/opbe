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
function calculateAttack(&$attackers, &$defenders, $FleetTF, $DefTF)
{
        global $pricelist, $CombatCaps, $resource;
        
        foreach ($attackers as $fleetID => $attacker)
        {
                $fleetObj = new Fleet($fleetID);
                foreach ($attacker['unit'] as $element => $amount)
                {
                        $fighters = getFighters($element,$amount);
                        $fleetObj->add($fighters);
                }
        }

}

function getFighters($id, $count)
{
    global $CombatCaps, $pricelist;
    $rf = $CombatCaps[$id]['sd'];
    $shield = $CombatCaps[$id]['shield'];
    $cost = array($pricelist[$element]['cost'][901], $pricelist[$element]['cost'][902]);
    $power = $CombatCaps[$id]['attack'];
    return new Ship($id, $count, $rf, $shield, $cost, $power);
}
?>