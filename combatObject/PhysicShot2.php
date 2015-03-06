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
 * @version beta(25-02-2015)
 * @link https://github.com/jstar88/opbe
 */

class PhysicShot2
{
    private $shipType;
    private $damage;
    private $count;

    private $assorbedDamage = 0;
    private $bouncedDamage = 0;
    private $hullDamage = 0;
    private $cellDestroyed = 0;
    
    
    /**
     * PhysicShot::__construct()
     * 
     * @param ShipType $shipType
     * @param int $damage
     * @param int $count
     * @return
     */
    public function __construct(ShipType $shipType, $damage, $count)
    {
        echo "damage=$damage<br>count=$count<br>";
        if ($damage < 0)
            throw new Exception('negative damage');
        if ($count < 0)
            throw new Exception('negative amount of shots');
        $this->fighters = $shipType->cloneMe();
        $this->damage = $damage;
        $this->count = $count;
    }
    
    
    /**
     * PhysicShot::getAssorbedDamage()
     * 
     * @return float
     */
    public function getAssorbedDamage($cell = false)
    {
        if ($cell)
        {
            return $this->cellDestroyed;
        }
        return $this->assorbedDamage;

    }
    
    
    /**
     * PhysicShot::getBouncedDamage()
     * 
     * @return float
     */
    public function getBouncedDamage()
    {
        return $this->bouncedDamage;
    }
    
    
    /**
     * PhysicShot::getHullDamage()
     * 
     * @return float
     */
    public function getHullDamage()
    {
        return $this->hullDamage;
    }
    
    
    /**
     * PhysicShot::getPureDamage()
     * Return the total amount of damage from enemy
     * @return int
     */
    public function getPureDamage()
    {
        return $this->damage * $this->count;
    }
    
    
    /**
     * PhysicShot::getHitShips()
     * Return the number of hitten ships.
     * @return
     */
    public function getHitShips()
    {
        return min($this->count, $this->fighters->getCount());
    }
    
    
    /**
     * PhysicShot::start()
     * Start the system
     * @return
     */
    public function start()
    {
        $n = $this->count;
        $d = $this->damage;
        $currentCellsCount = $this->fighters->getCurrentShield();
        if (USE_HITSHIP_LIMITATION)
        {
            $currentCellsCount = round($currentCellsCount * $this->getHitShips() / ( $this->fighters->getCount()));
        }
        $s_100 = $this->fighters->getShieldCellValue();
        $s = $currentCellsCount * $s_100;    
        $x = $this->clamp($d, $s_100);
        
        $this->bouncedDamage = ($d - $x)* $n;
        $this->assorbedDamage = min($x * $n, $s);
        
        $hullDamage = $this->getPureDamage() - $this->assorbedDamage - $this->bouncedDamage;
        $hullDamage = min($hullDamage, $this->fighters->getCurrentLife() * $this->getHitShips() / $this->fighters->getCount());
        $this->hullDamage = max(0, $hullDamage);
        
        // da sistemare, la suddivisione in celle dello scudo è inutile. 
        $this->cellDestroyed = ($s_100 == 0)?$currentCellsCount : min(round($this->assorbedDamage / $s_100),$currentCellsCount);
    }
    
    private function clamp($a,$b)
    {
        if ($a > $b)
        {
            return $a;
        }
        return 0;
    }
    
}