<?php

/**
 *  OPBE
 *  Copyright (C) 2015  Jstar
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
 * @copyright 2015 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version 6-3-1015
 * @link https://github.com/jstar88/opbe
 */

class ShipType extends Type
{

    private $originalPower;
    private $originalShield;

    private $singleShield;
    private $singleLife;
    private $singlePower;

    private $fullShield;
    private $fullLife;
    private $fullPower;

    protected $currentShield;
    protected $currentLife;

    private $weapons_tech = 0;
    private $shields_tech = 0;
    private $armour_tech = 0;

    private $rf;
    protected $lastShots;
    protected $lastShipHit;
    private $cost;

    /**
     * ShipType::__construct()
     * 
     * @param int $id
     * @param int $count
     * @param array $rf
     * @param int $shield
     * @param array $cost
     * @param int $power
     * @param int $weapons_tech
     * @param int $shields_tech
     * @param int $armour_tech
     * @return
     */
    public function __construct($id, $count, $rf, $shield, array $cost, $power, $weapons_tech = null, $shields_tech = null, $armour_tech = null)
    {
        parent::__construct($id, 0);

        $this->rf = $rf;
        $this->lastShots = 0;
        $this->lastShipHit = 0;
        $this->cost = $cost;

        $this->originalShield = $shield;
        $this->originalPower = $power;

        $this->singleShield = $shield;
        $this->singleLife = COST_TO_ARMOUR * array_sum($cost);
        $this->singlePower = $power;

        $this->increment($count);
        $this->setWeaponsTech($weapons_tech);
        $this->setArmourTech($armour_tech);
        $this->setShieldsTech($shields_tech);
    }
    
    
    /**
     * ShipType::setWeaponsTech()
     * 
     * @param int $level
     * @return null
     */
    public function setWeaponsTech($level)
    {
        if (!is_numeric($level))
            return;
        $level = intval($level);
        $diff = $level - $this->weapons_tech;
        if ($diff < 0)
            throw new Exception('Trying to decrease tech');
        $this->weapons_tech = $level;
        $incr = 1 + WEAPONS_TECH_INCREMENT_FACTOR * $diff;
        $this->singlePower *= $incr;
        $this->fullPower *= $incr;
    }
    
    
    /**
     * ShipType::setShieldsTech()
     * 
     * @param int $level
     * @return null
     */
    public function setShieldsTech($level)
    {
        if (!is_numeric($level))
            return;
        $level = intval($level);
        $diff = $level - $this->shields_tech;
        if ($diff < 0)
            throw new Exception('Trying to decrease tech');
        $this->shields_tech = $level;
        $incr = 1 + SHIELDS_TECH_INCREMENT_FACTOR * $diff;
        $this->singleShield *= $incr;
        $this->fullShield *= $incr;
        $this->currentShield *= $incr;
    }
    
    
    /**
     * ShipType::setArmourTech()
     * 
     * @param int $level
     * @return null
     */
    public function setArmourTech($level)
    {
        if (!is_numeric($level))
            return;
        $level = intval($level);
        $diff = $level - $this->armour_tech;
        if ($diff < 0)
            throw new Exception('Trying to decrease tech');
        $this->armour_tech = $level;
        $incr = 1 + ARMOUR_TECH_INCREMENT_FACTOR * $diff;
        $this->singleLife *= $incr;
        $this->fullLife *= $incr;
        $this->currentLife *= $incr;
    }


    /**
     * ShipType::increment()
     * 
     * @param int $number
     * @param int $newLife
     * @param int $newShield
     * @return null
     */
    public function increment($number, $newLife = null, $newShield = null)
    {
        parent::increment($number);
        if ($newLife == null)
        {
            $newLife = $this->singleLife;
        }
        if ($newShield == null)
        {
            $newShield = $this->singleShield;
        }
        $this->fullLife += $this->singleLife * $number;
        $this->fullPower += $this->singlePower * $number;
        $this->fullShield += $this->singleShield * $number;

        $this->currentLife += $newLife * $number;
        $this->currentShield += $newShield * $number;
    }
    
    
    /**
     * ShipType::decrement()
     * 
     * @param int $number
     * @param int $remainLife
     * @param int $remainShield
     * @return null
     */
    public function decrement($number, $remainLife = null, $remainShield = null)
    {
        parent::decrement($number);
        if ($remainLife == null)
        {
            $remainLife = $this->singleLife;
        }
        if ($remainShield == null)
        {
            $remainShield = $this->singleShield;
        }
        $this->fullLife -= $this->singleLife * $number;
        $this->fullPower -= $this->singlePower * $number;
        $this->fullShield -= $this->singleShield * $number;

        $this->currentLife -= $remainLife * $number;
        $this->currentShield -= $remainShield * $number;
    }
    
    
    /**
     * ShipType::setCount()
     * 
     * @param int $number
     * @return null
     */
    public function setCount($number)
    {
        parent::setCount($number);
        $diff = $number - $this->getCount();
        if ($diff > 0)
        {
            $this->increment($diff);
        }
        elseif ($diff < 0)
        {
            $this->decrement($diff);
        }
    }


    /**
     * ShipType::getCost()
     * 
     * @return array
     */
    public function getCost()
    {
        return $this->cost;
    }
    
    
    /**
     * ShipType::getWeaponsTech()
     * 
     * @return int
     */
    public function getWeaponsTech()
    {
        return $this->weapons_tech;
    }
    
    
    /**
     * ShipType::getShieldsTech()
     * 
     * @return int
     */
    public function getShieldsTech()
    {
        return $this->shields_tech;
    }
    
    
    /**
     * ShipType::getArmourTech()
     * 
     * @return int
     */
    public function getArmourTech()
    {
        return $this->armour_tech;
    }
    
    
    /**
     * ShipType::getRfTo()
     * 
     * @param ShipType $other
     * @return int
     */
    public function getRfTo(ShipType $other)
    {
        return (isset($this->rf[$other->getId()])) ? $this->rf[$other->getId()] : 0;
    }
    
    
    /**
     * ShipType::getRF()
     * 
     * @return array
     */
    public function getRF()
    {
        return $this->rf;
    }
    
    
    /**
     * ShipType::getShield()
     * 
     * @return int
     */
    public function getShield()
    {
        return $this->singleShield;
    }
    
    
    /**
     * ShipType::getShieldCellValue()
     * 
     * @return int
     */
    public function getShieldCellValue()
    {
        if ($this->isShieldDisabled())
        {
            return 0;
        }
        return $this->singleShield / SHIELD_CELLS;
    }
    
    
    /**
     * ShipType::getHull()
     * 
     * @return int
     */
    public function getHull()
    {
        return $this->singleLife;
    }
    
    
    /**
     * ShipType::getPower()
     * 
     * @return int
     */
    public function getPower()
    {
        return $this->singlePower;
    }
    
    
    /**
     * ShipType::getCurrentShield()
     * 
     * @return int
     */
    public function getCurrentShield()
    {
        return $this->currentShield;
    }
    
    
    /**
     * ShipType::getCurrentLife()
     * 
     * @return int
     */
    public function getCurrentLife()
    {
        return $this->currentLife;
    }


    /**
     * ShipType::inflictDamage()
     * 
     * @param int $damage
     * @param int $shotsToThisShipType
     * @return null
     */
    public function inflictDamage($damage, $shotsToThisShipType)
    {
        if ($shotsToThisShipType == 0)
            return;
        if ($shotsToThisShipType < 0)
            throw new Exception("Negative amount of shotsToThisShipType!");

        log_var('Defender single hull', $this->singleLife);
        log_var('Defender count', $this->getCount());
        log_var('currentShield before', $this->currentShield);
        log_var('currentLife before', $this->currentLife);

        $this->lastShots += $shotsToThisShipType;
        $ps = new PhysicShot($this, $damage, $shotsToThisShipType);
        $ps->start();
        $this->currentShield -= $ps->getAssorbedDamage();
        $this->currentLife -= $ps->getHullDamage();

        log_var('currentShield after', $this->currentShield);
        log_var('currentLife after', $this->currentLife);
        $this->lastShipHit += $ps->getHitShips();
        log_var('lastShipHit after', $this->lastShipHit);
        log_var('lastShots after', $this->lastShots);

        if ($this->currentLife < 0)
        {
            throw new Exception('Negative currentLife!');
        }
        if ($this->currentShield < 0)
        {
            throw new Exception('Negative currentShield!');
        }
        if ($this->lastShipHit < 0)
        {
            throw new Exception('Negative lastShipHit!');
        }
        return $ps; //for web
    }
    
    
    /**
     * ShipType::cleanShips()
     * 
     * @return ShipsCleaner
     */
    public function cleanShips()
    {
        log_var('lastShipHit after', $this->lastShipHit);
        log_var('lastShots after', $this->lastShots);
        log_var('currentLife before', $this->currentLife);

        $sc = new ShipsCleaner($this, $this->lastShipHit, $this->lastShots);
        $sc->start();
        $this->decrement($sc->getExplodedShips(), $sc->getRemainLife(), 0);
        $this->lastShipHit = 0;
        $this->lastShots = 0;
        log_var('currentLife after', $this->currentLife);
        return $sc;
    }
    
    
    /**
     * ShipType::repairShields()
     * 
     * @return null
     */
    public function repairShields()
    {
        $this->currentShield = $this->fullShield;
    }
    
    
    /**
     * ShipType::__toString()
     * 
     * @return null
     */
    public function __toString()
    {
        $return = parent::__toString();
        //$return .= "hull:" . $this->hull . "<br>Shield:" . $this->shield . "<br>CurrentLife:" . $this->currentLife . "<br>CurrentShield:" . $this->currentShield;
        return $return;
    }
    
    
    /**
     * ShipType::isShieldDisabled()
     * 
     * @return boolean
     */
    public function isShieldDisabled()
    {
        return $this->currentShield < 0.01;
    }
    
    
    /**
     * ShipType::cloneMe()
     * 
     * @return ShipType
     */
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
