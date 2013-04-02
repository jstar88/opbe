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
class Fighters extends Type
{
    private $rf;
    private $shield;
    private $hull;
    private $power;
    // at cells and fusion
    private $currentShield;
    // as fusion
    private $currentLife;

    private $weapons_tech;
    private $shields_tech;
    private $armour_tech;

    private $lastShots;
    private $lastShipHit;
    
    private $cost;

    public function __construct($id, $count, $rf, $shield, array $cost, $power, $weapons_tech = 0, $shields_tech = 0, $armour_tech = 0)
    {
        parent::__construct($id, $count);

        $hull = COST_TO_ARMOUR * array_sum($cost);
        $this->rf = $rf;
        $this->shield = $shield + SHIELDS_TECH_INCREMENT_FACTOR * $shields_tech * $shield;
        $this->hull = $hull + ARMOUR_TECH_INCREMENT_FACTOR * $armour_tech * $hull;
        $this->power = $power + WEAPONS_TECH_INCREMENT_FACTOR * $weapons_tech * $power;
        $this->currentShield = SHIELD_CELLS * $count;
        $this->currentLife = $this->hull * $count;

        $this->weapons_tech = $weapons_tech;
        $this->shields_tech = $shields_tech;
        $this->armour_tech = $armour_tech;
        $this->lastShots = 0;
        $this->lastShipHit = 0;
        $this->cost = $cost;
    }
    public function getCost()
    {
        return $this->cost;
    }
    public function setWeaponsTech($level)
    {
        $diff = $level - $this->weapons_tech;
        $this->weapons_tech = $level;
        $this->power += WEAPONS_TECH_INCREMENT_FACTOR * $diff * $this->power;
    }
    public function setShieldsTech($level)
    {
        $diff = $level - $this->shields_tech;
        $this->shields_tech = $level;
        $this->shield += SHIELDS_TECH_INCREMENT_FACTOR * $diff * $this->shield;
    }
    public function setArmourTech($level)
    {
        $diff = $level - $this->armour_tech;
        $this->armour_tech = $level;
        $this->hull += ARMOUR_TECH_INCREMENT_FACTOR * $diff * $this->hull;
        $this->currentLife += ARMOUR_TECH_INCREMENT_FACTOR * $diff * $this->currentLife;
    }
    public function getRfTo(Fighters $other)
    {
        return (isset($this->rf[$other->getId()]))? $this->rf[$other->getId()] : 0 ;
    }
    public function getShield()
    {
        return $this->shield;
    }
    public function getShieldCellValue()
    {
        return $this->shield / SHIELD_CELLS;
    }
    public function getHull()
    {
        return $this->hull;
    }
    public function getPower()
    {
        return $this->power;
    }
    public function getCurrentShield()
    {
        return $this->currentShield;
    }
    public function getCurrentLife()
    {
        return $this->currentLife;
    }
    public function inflictShots($damage, $colpiSparatiVersoQuestoTipoDiNavi)
    {
        if ($colpiSparatiVersoQuestoTipoDiNavi == 0)
            return;
        if ($colpiSparatiVersoQuestoTipoDiNavi < 0)
            throw new Exception("negative count!");
        echo 'Defender single hull='.$this->hull.'<br>';
        echo 'Defender count='.$this->getCount().'<br>';
        $this->lastShots += $colpiSparatiVersoQuestoTipoDiNavi;
        echo "currentShield before= {$this->currentShield}<br>"; 
        echo "currentLife before={$this->currentLife}<br>";
        $ps = new PhysicShot($this, $damage, $colpiSparatiVersoQuestoTipoDiNavi);
        $ps->start();
        $this->currentShield -= $ps->getAssorbedDamage(true);
        $this->currentLife -= $ps->getHullDamage();
        echo "currentShield after= {$this->currentShield}<br>"; 
        echo "currentLife after={$this->currentLife}<br>";
        $this->lastShipHit += $ps->getHitShips();
        echo "lastShipHit after = $this->lastShipHit<br>";
        echo "lastShots after={$this->lastShots}<br>";
        return $ps; //for web
    }
    public function cleanShips()
    {
        echo "lastShipHit after = $this->lastShipHit<br>";
        echo "lastShots after={$this->lastShots}<br>";
        echo "currentLife before={$this->currentLife}<br>";
        $sc = new ShipsCleaner($this, $this->lastShipHit, $this->lastShots);
        $sc->start();
        $this->decrement($sc->getExplodedShips());
        $this->currentLife -= $sc->getRemainLife();
        $this->lastShipHit = 0;
        $this->lastShots = 0;
        echo "currentLife after={$this->currentLife}<br>";
        return $sc;
    }
    public function repairShields()
    {
        $this->currentShield = SHIELD_CELLS * $this->getCount();
    }
    public function repairHull()
    {
        $this->currentLife = $this->hull * $this->getCount();
    }
    public function __toString()
    {
        $return = parent::__toString();
        //$return .= "hull:" . $this->hull . "<br>Shield:" . $this->shield . "<br>CurrentLife:" . $this->currentLife . "<br>CurrentShield:" . $this->currentShield;
        return $return;
    }
    public function isShieldDisabled()
    {
        return $this->currentShield == 0;
    }
}
