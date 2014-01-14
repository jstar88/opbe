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
class ShipType extends Type
{
    private $rf;
    
    //only used to clone
    private $originalShield;
    private $originalHull;
    private $originalPower;
        
    private $shield;
    private $hull;
    private $power;
    // at cells and fusion
    protected $currentShield;
    // as fusion
    protected $currentLife;

    private $weapons_tech = 0;
    private $shields_tech = 0;
    private $armour_tech = 0;

    protected $lastShots;
    protected $lastShipHit;
    
    private $cost;

    public function __construct($id, $count, $rf, $shield, array $cost, $power, $weapons_tech = null, $shields_tech = null, $armour_tech = null)
    {
        parent::__construct($id, $count);

        $this->rf = $rf;
        $this->originalShield = $this->shield = $shield;
        $this->originalHull = $this->hull = COST_TO_ARMOUR * array_sum($cost);
        $this->originalPower = $this->power = $power;
        $this->currentShield = SHIELD_CELLS * $count;
        $this->currentLife = $this->hull * $count;
        $this->lastShots = 0;
        $this->lastShipHit = 0;
        $this->cost = $cost;
        
        $this->setWeaponsTech($weapons_tech);
        $this->setArmourTech($armour_tech);
        $this->setShieldsTech($shields_tech);
    }
    public function getCost()
    {
        return $this->cost;
    }
    public function setWeaponsTech($level)
    {
        if(!is_numeric($level)) return;
        $level = intval($level);
        $diff = $level - $this->weapons_tech;
        if($diff < 0) throw new Exception('trying to decrease tech');
        $this->weapons_tech = $level;
        $this->power += WEAPONS_TECH_INCREMENT_FACTOR * $diff * $this->power;
    }
    public function setShieldsTech($level)
    {
        if(!is_numeric($level)) return;
        $level = intval($level);
        $diff = $level - $this->shields_tech;
        if($diff < 0) throw new Exception('trying to decrease tech');
        $this->shields_tech = $level;
        $this->shield += SHIELDS_TECH_INCREMENT_FACTOR * $diff * $this->shield;
    }
    public function setArmourTech($level)
    {
        if(!is_numeric($level)) return;
        $level = intval($level);
        $diff = $level - $this->armour_tech;
        if($diff < 0) throw new Exception('trying to decrease tech');
        $this->armour_tech = $level;
        $this->hull += ARMOUR_TECH_INCREMENT_FACTOR * $diff * $this->hull;
        $this->currentLife += ARMOUR_TECH_INCREMENT_FACTOR * $diff * $this->currentLife;
    }
    public function getWeaponsTech()
    {
        return $this->weapons_tech;    
    }
    public function getShieldsTech()
    {
        return $this->shields_tech;
    }
    public function getArmourTech()
    {
        return $this->armour_tech;
    }
    public function getRfTo(ShipType $other)
    {
        return (isset($this->rf[$other->getId()]))? $this->rf[$other->getId()] : 0 ;
    }
    public function getRF()
    {
        return $this->rf;
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
    public function getOriginalPower()
    {
        return $this->originalPower;
    }
    public function getOriginalShield()
    {
        return $this->originalShield;
    }
    public function getCurrentShield()
    {
        return $this->currentShield;
    }
    public function getCurrentLife()
    {
        return $this->currentLife;
    }
    public function inflictDamage($damage, $colpiSparatiVersoQuestoTipoDiNavi)
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
        
        // strange bug
        if($this->currentLife < 0 && abs($this->currentLife) < 1e-9)
        {
            $this->currentLife = 0;
        }
        
        echo "currentShield after= {$this->currentShield}<br>"; 
        echo "currentLife after={$this->currentLife}<br>";
        $this->lastShipHit += $ps->getHitShips();
        echo "lastShipHit after = $this->lastShipHit<br>";
        echo "lastShots after={$this->lastShots}<br>";
        if($this->currentLife < 0 || $this->currentShield < 0 || $this->lastShipHit < 0)
        {
            throw new Exception('negative count!');
        }
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
    public function cloneMe()
    {
        $class = get_class($this);
        $tmp = new $class($this->getId(), $this->getCount(), $this->rf, $this->originalShield, $this->cost, $this->originalPower, $this->weapons_tech, $this->shields_tech, $this->armour_tech);
        $tmp->currentShield = $this->currentShield;
        $tmp->currentLife = $this->currentLife;
        $tmp->lastShots = $this->lastShots;
        $tmp->lastShipHit = $this->lastShipHit;
        return $tmp;
    }
}
