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
class ShipsCleaner
{
    private $fighters;
    private $lastShipHit;
    private $lastShots;

    private $exploded;
    private $remainLife;
    /**
     * ShipsCleaner::__construct()
     * 
     * @param mixed $fighters
     * @param int $lastShipHit
     * @param int $lastShot
     * @return ShipsCleaner
     */
    public function __construct(Fighters $fighters, $lastShipHit, $lastShots)
    {
        $this->fighters = $fighters;
        $this->lastShipHit = $lastShipHit;
        $this->lastShots = $lastShots;
    }
    /**
     * ShipsCleaner::start()
     * Start the system
     * @return null
     */
    public function start()
    {     
        $prob = 1 - $this->fighters->getCurrentLife() / ($this->fighters->getHull() * $this->fighters->getCount());
        echo "prob=$prob<br>";
        if ($prob < 0)
        {
            throw new Exception("negative prob");
        }
        if ($this->lastShipHit >= $this->fighters->getCount()/PROB_TO_REAL_MAGIC)
        {
            echo "this->lastShipHit >= this->fighters->getCount()/2<br>";
            if ($prob < MIN_PROB_TO_EXPLODE)
            {
                $probToExplode = 0;
            }
            else
            {
                $probToExplode = $prob;
            }
        }
        else
        {
            echo "this->lastShipHit < this->fighters->getCount()/2<br>";
            $probToExplode = $prob * (1 - MIN_PROB_TO_EXPLODE);
        }
        
        $teoricExploded = $this->fighters->getCount() * $probToExplode;
        $this->exploded = min(floor($teoricExploded), $this->lastShots);

        //tolgo la vita rimanente delle navi esplose
        $this->remainLife = $this->exploded * (1 - $prob) * ($this->fighters->getCurrentLife() / $this->fighters->getCount());
        //se tolgo una nave non interamente distrutta,vado ad incrementare la vita o viceversa
        //$this->currentLife -= ($teoricExploded-$exploded)*$this->hull*(1-$prob);
        echo "probToExplode = $probToExplode<br>$teoricExploded = teoricExploded<br>";
        echo "exploded ={$this->exploded}<br>";
        echo "remainLife = {$this->remainLife}<br>";
    }
    /**
     * ShipsCleaner::getExplodeShips()
     * Return the number of exploded ships
     * @return int
     */
    public function getExplodedShips()
    {
        return $this->exploded;
    }
    /**
     * ShipsCleaner::getRemainLife()
     * Return the life of exploded ships
     * @return float
     */
    public function getRemainLife()
    {
        return $this->remainLife;
    }

}
