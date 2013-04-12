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
 
class PhysicShot
{
    private $fighters;
    private $damage;
    private $count;

    private $assorbedDamage = 0;
    private $bouncedDamage = 0;
    private $hullDamage = 0;
    private $cellDestroyed = 0;
    /**
     * PhysicShot::__construct()
     * 
     * @param Fighters $fighters
     * @param int $damage
     * @param int $count
     * @return
     */
    public function __construct(Fighters $fighters, $damage, $count)
    {
        echo "damage=$damage<br>count=$count<br>";
        $this->fighters = $fighters;
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
        if($this->damage == 0) return;
        $hitShips = $this->getHitShips();
        $shieldCellValue = $this->fighters->getShieldCellValue();
        if($shieldCellValue == 0)
        {
            $this->inflict($hitShips);
            return;    
        }
        $dv = Math::divide(new Number($this->damage), new Number($this->fighters->getShieldCellValue()), true);
        $cellsDestroyedInOneShot = $dv->result;
        $bouncedDamageForOneShot = $dv->rest;
        // bisogna tenere solo i colpi neccessari alla distruzione di tutti gli scudi
        $currentCellsCount = floor($this->fighters->getCurrentShield() * $hitShips / $this->fighters->getCount());
        echo "shieldCellValue=".$this->fighters->getShieldCellValue()."<br>";
        echo "cellsDestroyedInOneShot=$cellsDestroyedInOneShot<br>bouncedDamageForOneShot=$bouncedDamageForOneShot<br>currentCellsCount=$currentCellsCount<br>";
        $this->bounce($currentCellsCount, $cellsDestroyedInOneShot, $bouncedDamageForOneShot);
        $this->assorb($currentCellsCount, $cellsDestroyedInOneShot);
        $this->inflict($hitShips);
    }
    /**
     * PhysicShot::bounce()
     * If the shield is disabled, then bounced damaged is zero.
     * If the damage is exactly a multipler of the needed to destroy one shield's cell then bounced damage is zero. 
     * If damage is more than shield,then bounced damage is zero.
     * 
     * @param int $currentCellsCount
     * @param int $cellsDestroyedInOneShot
     * @param float $bouncedDamageForOneShot
     * @return null
     */
    private function bounce($currentCellsCount, $cellsDestroyedInOneShot, $bouncedDamageForOneShot)
    {
        echo "bounce function<br>";
        if ($this->damage > $this->fighters->getCurrentShield())
        {
            $this->bouncedDamage = 0;
            echo "bouncedDamage = 0<br>";
            return;
        }
        if($cellsDestroyedInOneShot == 0)
        {
            $this->bouncedDamage = $this->damage * $this->count;  
            return;       
        }
        $numeroDiColpiPerDistruggereTuttiGliScudi = $currentCellsCount / $cellsDestroyedInOneShot;
        $this->bouncedDamage = min($numeroDiColpiPerDistruggereTuttiGliScudi,$this->count) * $bouncedDamageForOneShot;
        //$colpiAsegno = max(0, $this->count - $numeroDiColpiPerDistruggereTuttiGliScudi);
        echo "numeroDiColpiPerDistruggereTuttiGliScudi=$numeroDiColpiPerDistruggereTuttiGliScudi<br>";
        echo "bouncedDamage={$this->bouncedDamage}<br>";
    }
    /**
     * PhysicShot::assorb()
     * If the shield is disabled, then assorbed damaged is zero.
     * If the total damage is more than shield, than the assorbed damage should equal the shield value.
     * @param int $currentCellsCount
     * @param int $cellsDestroyedInOneShot
     * @return null
     */
    private function assorb($currentCellsCount, $cellsDestroyedInOneShot)
    {
        echo "assorb function<br>";
        $totalCellsDestroyedAtMax = $cellsDestroyedInOneShot * $this->count;
        $realTotalCellsDestroyed = floor(min($totalCellsDestroyedAtMax, $currentCellsCount));
        $this->assorbedDamage = $realTotalCellsDestroyed * $this->fighters->getShieldCellValue();
        $this->cellDestroyed = $realTotalCellsDestroyed;
        
        echo "totalCellsDestroyedAtMax = $totalCellsDestroyedAtMax<br>realTotalCellsDestroyed=$realTotalCellsDestroyed<br>assorbedDamage={$this->assorbedDamage}<br>";
    }
    /**
     * PhysicShot::inflict()
     * HullDamage should be more than zero and less than shiplife.
     * Expecially, it should be less than the life of hitten ships.
     * @return null
     */
    private function inflict($hitShips)
    {
        echo "inflict function<br>";
        $hullDamage = $this->getPureDamage() - $this->assorbedDamage - $this->bouncedDamage;
        //il danno non può essere superiore alla vita delle navi colpite
        $hullDamage = min($hullDamage, $this->fighters->getCurrentLife() * $hitShips / $this->fighters->getCount());
        $this->hullDamage = max(0, $hullDamage);
        
        echo "hullDamage=$hullDamage<br>hullDamage={$this->hullDamage}<br>";
    }
}
