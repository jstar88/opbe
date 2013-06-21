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
class BattleReport
{
    private $rounds;
    private $roundsCount;

    private $attackersLostUnits;
    private $defendersLostUnits;

    public function __construct()
    {
        $this->rounds = array();
        $this->roundsCount = 0;
        $this->attackersLostUnits = null;
        $this->defendersLostUnits = null;
    }
    public function addRound(Round $round)
    {
        if (ONLY_FIRST_AND_LAST_ROUND && $this->roundsCount == 2)
        {
            $this->rounds[1] = $round;
            return;
        }
        $this->rounds[$this->roundsCount++] = $round;
    }
    public function getRound($number)
    {
        if ($number === 'END')
        {
            return $this->rounds[$this->roundsCount - 1];
        }
        elseif ($number === 'START')
        {
            return $this->rounds[0];
        }
        elseif (intval($number) < 0 || intval($number) > $this->getLastRoundNumber())
        {
            throw new Exception('Invalid round number');
        }
        else
        {
            return $this->rounds[intval($number)];
        }
    }
    public function setBattleResult($att, $def)
    {
        $this->getRound('END')->getAttachersAfterRound()->battleResult = $att;
        $this->getRound('END')->getDefendersAfterRound()->battleResult = $def;
    }
    public function attackerHasWin()
    {
        return $this->getRound('END')->getAttachersAfterRound()->battleResult === BATTLE_WIN;
    }
    public function defenderHasWin()
    {
        return $this->getRound('END')->getDefendersAfterRound()->battleResult === BATTLE_WIN;
    }
    public function isAdraw()
    {
        return $this->getRound('END')->getAttachersAfterRound()->battleResult === BATTLE_DRAW;
    }
    private function getPresentationRound($number)
    {
        if ($number !== 'START')
        {
            $number -= 1;
        }
        return $this->getRound($number);
    }
    private function getResultRound($number)
    {
        return $this->getRound($number);
    }
    public function getPresentationAttackersFleetOnRound($number)
    {
        return $this->getPresentationRound($number)->getAttachersAfterRound();
    }
    public function getPresentationDefendersFleetOnRound($number)
    {
        return $this->getPresentationRound($number)->getDefendersAfterRound();
    }
    public function getResultAttackersFleetOnRound($number)
    {
        return $this->getResultRound($number)->getAttachersAfterRound();
    }
    public function getResultDefendersFleetOnRound($number)
    {
        return $this->getResultRound($number)->getDefendersAfterRound();
    }
    public function getTotalAttackersLostUnits()
    {
        return Math::recursive_sum($this->getAttackersLostUnits());
    }
    public function getTotalDefendersLostUnits()
    {
        return Math::recursive_sum($this->getDefendersLostUnits());
    }
    public function getAttackersLostUnits()
    {
        if ($this->attackersLostUnits !== null)
        {
            return $this->attackersLostUnits;
        }
        $attackersBefore = $this->getRound('START')->getAttachersAfterRound();
        $attackersAfter = $this->getRound('END')->getAttachersAfterRound();
        return $this->getPlayersLostUnits($attackersBefore, $attackersAfter);
    }
    public function getDefendersLostUnits()
    {
        if ($this->defendersLostUnits !== null)
        {
            return $this->defendersLostUnits;
        }
        $defendersBefore = $this->getRound('START')->getDefendersAfterRound();
        $defendersAfter = $this->getRound('END')->getDefendersAfterRound();
        return $this->getPlayersLostUnits($defendersBefore, $defendersAfter);
    }
    private function getPlayersLostUnits(PlayerGroup $playersBefore, PlayerGroup $playersAfter)
    {
        $lostShips = $this->getPlayersLostShips($playersBefore, $playersAfter);
        $defRepaired = $this->getPlayerRepaired($playersBefore, $playersAfter);
        $return = array();
        foreach ($lostShips->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $cost = $fighters->getCost();
                    $repairedAmount = 0;
                    if($defRepaired->getPlayer($idPlayer) !== false && $defRepaired->getPlayer($idPlayer)->getFleet($idFleet) !== false && $defRepaired->getPlayer($idPlayer)->getFleet($idFleet)->getFighters($idFighters) !== false)
                    {
                        $repairedAmount = $defRepaired->getPlayer($idPlayer)->getFleet($idFleet)->getFighters($idFighters)->getCount();    
                    }
                    $count = $fighters->getCount() - $repairedAmount;
                    if ($count > 0)
                    {
                        $return[$idPlayer][$idFleet][$idFighters] = array($cost[0] * $count, $cost[1] * $count);
                    }
                    elseif ($count < 0)
                    {
                        throw new Exception('Count negative');
                    }
                }
            }
        }
        return $return;
    }
    public function tryMoon()
    {
        $prob = $this->getMoonProb();
        return Math::tryEvent($prob, 'Events::event_moon', $prob);
    }
    public function getMoonProb()
    {
        return min(round(array_sum($this->getDebris()) / MOON_UNIT_PROB), MAX_MOON_PROB);
    }
    public function getAttackerDebris()
    {
        $metal = 0;
        $crystal = 0;
        foreach ($this->getAttackersLostUnits() as $idPlayer => $player)
        {
            foreach ($player as $idFleet => $fleet)
            {
                foreach ($fleet as $idFighters => $lost)
                {
                    $metal += $lost[0];
                    $crystal += $lost[1];
                }
            }
        }
        return array($metal * DEBRIS_FACTOR, $crystal * DEBRIS_FACTOR);
    }
    public function getDefenderDebris()
    {
        $metal = 0;
        $crystal = 0;
        foreach ($this->getDefendersLostUnits() as $idPlayer => $player)
        {
            foreach ($player as $idFleet => $fleet)
            {
                foreach ($fleet as $idFighters => $lost)
                {
                    $metal += $lost[0];
                    $crystal += $lost[1];
                }
            }
        }
        return array($metal * DEBRIS_FACTOR, $crystal * DEBRIS_FACTOR);
    }
    public function getDebris()
    {
        $aDebris = $this->getAttackerDebris();
        $dDebris = $this->getDefenderDebris();
        return array($aDebris[0] + $dDebris[0], $aDebris[1] + $dDebris[1]);
    }
    public function getAttackersFirePower($round)
    {
        return $this->getRound($round)->getAttackersFire()->getAttacherTotalFire();
    }
    public function getAttackersFireCount($round)
    {
        return $this->getRound($round)->getAttackersFire()->getAttackerTotalShots();
    }
    public function getDefendersFirePower($round)
    {
        return $this->getRound($round)->getDefendersFire()->getAttacherTotalFire();
    }
    public function getDefendersFireCount($round)
    {
        return $this->getRound($round)->getDefendersFire()->getAttackerTotalShots();
    }
    private function getPlayersAssorbedDamage($playerGroupPS)
    {
        $ass = 0;
        foreach ($playerGroupPS as $idPlayer => $playerPs)
        {
            foreach ($playerPs as $idFleet => $fleetPS)
            {
                foreach ($fleetPS as $idTypeD => $typeDPS)
                {
                    foreach ($typeDPS as $idTypeA => $typeAPS)
                    {
                        $ass += $typeAPS->getAssorbedDamage();
                    }
                }
            }
        }
        return $ass;
    }
    public function getAttachersAssorbedDamage($round)
    {
        $playerGroupPS = $this->getRound($round)->getDefendersPhysicShots();
        return $this->getPlayersAssorbedDamage($playerGroupPS);
    }
    public function getDefendersAssorbedDamage($round)
    {
        $playerGroupPS = $this->getRound($round)->getAttachersPhysicShots();
        return $this->getPlayersAssorbedDamage($playerGroupPS);
    }
    public function getAttackersTech()
    {
        $techs = array();
        $players = $this->getRound('START')->getAttachersAfterRound()->getIterator();
        foreach ($players->getIterator() as $id => $player)
        {
            $techs[$player->getId()] = array(
                $player->getWeaponsTech(),
                $player->getShieldsTech(),
                $player->getArmourTech());
        }
        return $techs;
    }
    public function getDefendersTech()
    {
        $techs = array();
        $players = $this->getRound('START')->getDefendersAfterRound()->getIterator();
        foreach ($players->getIterator() as $id => $player)
        {
            $techs[$player->getId()] = array(
                $player->getWeaponsTech(),
                $player->getShieldsTech(),
                $player->getArmourTech());
        }
        return $techs;
    }
    public function getLastRoundNumber()
    {
        return $this->roundsCount - 1;
    }
    public function toString($resource)
    {
        ob_start();
        require ("../../views/report.html");
        return ob_get_clean();
    }
    private function getPlayerRepaired($playersBefore, $playersAfter)
    {
        $lostShips = $this->getPlayersLostShips($playersBefore, $playersAfter);
        foreach ($lostShips->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $lostShips->decrement($idPlayer, $idFleet, $idFighters, floor($fighters->getCount() * (1 - $fighters->getRepairProb())));
                }
            }
        }
        return $lostShips;
    }
    public function getDefendersRepaired()
    {
        $defendersBefore = $this->getRound('START')->getDefendersAfterRound();
        $defendersAfter = $this->getRound('END')->getDefendersAfterRound();
        return $this->getPlayerRepaired($defendersBefore, $defendersAfter);
    }
    public function getAttackersRepaired()
    {
        $attackersBefore = $this->getRound('START')->getAttachersAfterRound();
        $attackersAfter = $this->getRound('END')->getAttachersAfterRound();
        return $this->getPlayerRepaired($attackersBefore, $attackersAfter);
    }
    private function getPlayersLostShips(PlayerGroup $playersBefore, PlayerGroup $playersAfter)
    {
        $playersBefore_clone = DeepClonable::cloneIt($playersBefore);

        foreach ($playersAfter->getIterator() as $idPlayer => $playerAfter)
        {
            foreach ($playerAfter->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $playersBefore_clone->decrement($idPlayer, $idFleet, $idFighters, $fighters->getCount());
                }
            }
        }
        return $playersBefore_clone;
    }
    public function getAfterBattleAttackers()
    {
        $players = $this->getResultAttackersFleetOnRound('END');
        $playersRepaired = $this->getAttackersRepaired();
        $players = DeepClonable::cloneIt($players);

        foreach ($playersRepaired->getIterator() as $idPlayer => $player)
        {
            $endPlayer = $players->getPlayer($idPlayer);
            if ($endPlayer === false) // player is completely destroyed
            {
                $endPlayer = $player;
                $players->addPlayer($endPlayer);
                continue;
            }
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                $endFleet = $endPlayer->getFleet($idFleet);
                if ($endFleet === false)
                {
                    $endFleet = $fleet;
                    $endPlayer->addFleet($endFleet);
                    continue;
                }
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $endFighters = $endFleet->getFighters($idFighters);
                    if ($endFighters === false)
                    {
                        $endFighters = $fighters;
                    }
                    else
                    {
                        $endFighters->increment($fighters->getCount());
                    }
                }
            }
        }
        return $players;
    }
    public function getAfterBattleDefenders()
    {
        $players = $this->getResultDefendersFleetOnRound('END');
        $playersRepaired = $this->getDefendersRepaired();
        $players = DeepClonable::cloneIt($players);

        foreach ($playersRepaired->getIterator() as $idPlayer => $player)
        {
            $endPlayer = $players->getPlayer($idPlayer);
            if ($endPlayer === false) // player is completely destroyed
            {
                $endPlayer = $player;
                $players->addPlayer($endPlayer);
                continue;
            }
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                $endFleet = $endPlayer->getFleet($idFleet);
                if ($endFleet === false)
                {
                    $endFleet = $fleet;
                    $endPlayer->addFleet($endFleet);
                    continue;
                }
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $endFighters = $endFleet->getFighters($idFighters);
                    if ($endFighters === false)
                    {
                        $endFighters = $fighters;
                    }
                    else
                    {
                        $endFighters->increment($fighters->getCount());
                    }
                }
            }
        }
        return $players;
    }
}
