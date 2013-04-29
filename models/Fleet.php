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
class Fleet extends DeepClonable
{
    protected $array = array();
    private $count;
    private $id;
    public function __construct($id, $types = array())
    {
        $this->id = $id;
        $this->count = 0;
        foreach ($types as $type)
        {
            $this->add($type);
        }
    }
    public function getId()
    {
        return $this->id;
    }
    public function setTech($weapons, $shields, $armour)
    {
        foreach ($this->array as $id => $fighters)
        {
            $fighters->setWeaponsTech($weapons);
            $fighters->setShieldsTech($shields);
            $fighters->setArmourTech($armour);
        }
    }
    public function add(Fighters $type)
    {
        if (isset($this->array[$type->getId()]))
        {
            $this->array[$type->getId()]->increment($type->getCount());
        }
        else
        {
            $this->array[$type->getId()] = $type;
        }
        $this->count += $type->getCount();
    }
    public function decrement($id, $count)
    {
        $this->array[$id]->decrement($count);
        if ($this->array[$id]->getCount() <= 0)
        {
            unset($this->array[$id]);
        }
    }
    public function mergeFleet(Fleet $other)
    {
        foreach ($other->getIterator() as $type)
        {
            $this->add($type);
        }
    }
    public function getIterator()
    {
        return $this->array;
    }
    public function getFighters($id)
    {
        return isset($this->array[$id]) ? $this->array[$id] : false;
    }
    public function getTypeCount($type)
    {
        return $this->array[$type]->getCount();
    }
    public function getTotalCount()
    {
        return $this->count;
    }
    public function __toString()
    {
        if ($this->isEmpty())
            return "Destroyed<br>";
        $string = "";
        ksort($this->array);
        foreach ($this->array as $id => $fighters)
        {
            $string .= $fighters . "________<br>";
        }
        return $string;
    }
    public function inflictDamage(FireManager $fires)
    {
        $physicShots = array();
        foreach ($fires->getIterator() as $idf => $fire)
        {
            foreach ($this->getOrderedIterator() as $id => $defenders)
            {
                $ida = $fire->getId();
                echo "---- firing from $ida to $id ---- <br>";
                $xs = $fire->getShotsFiredByAllToDefenderType($defenders, true);
                $ps = $defenders->inflictShots($fire->getPower(), $xs->result);
                if ($ps != null)
                    $physicShots[$id][$idf] = $ps;

            }

        }
        return $physicShots;
    }
    public function getOrderedIterator()
    {
        if (!ksort($this->array))
        {
            throw new Exception('Unable to order types');
        }
        return $this->array;
    }


    public function cleanShips()
    {
        $shipsCleaners = array();
        foreach ($this->array as $id => $defenders)
        {
            echo "---- exploding $id ----<br>";
            $sc = $defenders->cleanShips();
            $this->count -= $sc->getExplodedShips();
            if ($defenders->isEmpty())
            {
                unset($this->array[$id]);
            }
            $shipsCleaners[$defenders->getId()] = $sc;
        }
        return $shipsCleaners;
    }
    public function repairShields()
    {
        foreach ($this->array as $id => $defenders)
        {
            $defenders->repairShields();
        }
    }
    public function repairHull()
    {
        foreach ($this->array as $id => $defenders)
        {
            $defenders->repairHull();
        }
    }
    public function isEmpty()
    {
        foreach ($this->array as $id => $fighters)
        {
            if (!$fighters->isEmpty())
            {
                return false;
            }
        }
        return true;
    }
}
