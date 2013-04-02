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
class Round
{
    private $attackers;
    private $defenders;

    private $fire_a;
    private $fire_d;

    private $physicShotsToDefenders;
    private $physicShotsToAttachers;

    private $attacherShipsCleaner;
    private $defenderShipsCleaner;

    private $number;

    public function __construct(PlayerGroup $attackers, PlayerGroup $defenders, $number)
    {
        $this->number = $number;
        $this->fire_a = new FireManager();
        $this->fire_d = new FireManager();
        // we clone to avoid collateral effects
        if (USE_SERIALIZATION_TO_CLONE)
        {
            $this->attackers = DeepClonable::cloneIt($attackers);
            $this->defenders = DeepClonable::cloneIt($defenders);
        }
        else
        {
            DeepClonable::$useSerialization = USE_PARTIAL_SERIALIZATION_TO_CLONE;
            $this->attackers = clone $attackers;
            $this->defenders = clone $defenders;
        }
    }
    public function startRound()
    {
        echo '--- Round '.$this->number.' ---<br><br>';
        $defendersMerged = $this->defenders->getEquivalentFleetContent();
        foreach ($this->attackers->getIterator() as  $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach($fleet->getIterator() as $idFighters => $fighters)
                {
                    $this->fire_a->add(new Fire($fighters, $defendersMerged));    
                }
            }
        }    
        $attackersMerged = $this->attackers->getEquivalentFleetContent();
        foreach ($this->defenders->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach($fleet->getIterator() as $idFighters => $fighters)
                {
                    $this->fire_d->add(new Fire($fighters, $attackersMerged));
                }
            }
        }
        
        $this->physicShotsToDefenders = $this->defenders->inflictDamage($this->fire_a);
        $this->physicShotsToAttachers = $this->attackers->inflictDamage($this->fire_d);
        
        $this->defenderShipsCleaner = $this->defenders->cleanShips();
        $this->attacherShipsCleaner = $this->attackers->cleanShips();
        
        $this->defenders->repairShields();                
        $this->attackers->repairShields();
        #//first merge all fleets to calculate a right RF
#        $attackersMerged = $this->attackers->getEquivalentFleetContent();
#        $defendersMerged = $this->defenders->getEquivalentFleetContent();
#        $this->fire_a = new Fire($attackersMerged, $defendersMerged);
#        $this->fire_d = new Fire($defendersMerged, $attackersMerged);
#        //inflict the fire to defenders
#        $this->physicShotsToDefenders = $this->defenders->inflictDamage($this->fire_a, $attackersMerged);
#        //inflict the fire to attackers
#        $this->physicShotsToAttachers = $this->attackers->inflictDamage($this->fire_d, $defendersMerged);
#        //clean ships
#        $this->defenders->cleanShips();
#        $this->attackers->cleanShips();
#        //repair shields
#        $this->defenders->repairShields();
#        $this->attackers->repairShields();
    }
    public function getAttackersFire()
    {
        return $this->fire_a;
    }
    public function getDefendersFire()
    {
        return $this->fire_d;
    }
    public function getAttachersPhysicShots()
    {
        return $this->physicShotsToDefenders;
    }
    public function getDefendersPhysicShots()
    {
        return $this->physicShotsToAttachers;
    }
    public function getAttachersShipsCleaner()
    {
        return $this->attacherShipsCleaner;
    }
    public function getDefendersShipsCleaner()
    {
        return $this->defenderShipsCleaner;
    }
    public function getAttachersAfterRound()
    {
        return $this->attackers;
    }
    public function getDefendersAfterRound()
    {
        return $this->defenders;
    }
    public function __toString()
    {
        return 'Round: ' . $this->number . '<br>Attackers:' . $this->attackers . '<br>Defenders:' . $this->defenders;

    }
    public function getNumber()
    {
        return $this->number;
    }
}
