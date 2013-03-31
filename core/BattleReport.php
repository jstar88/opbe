<?php
/**
 * 
 * 
 * @package   
 * @author Jstar
 * @copyright Jstar
 * @version 2013
 * @access public
 */
class BattleReport
{
    private $rounds;
    private $roundsCount;

    private $attackersLostUnits;
    private $defendersLostUnits;

    private $playerLostShips;
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
        $this->rounds[] = $round;
        $this->roundsCount++;
    }
    public function getRound($number)
    {
        if ($number === 'END')
        {
            return $this->rounds[$this->roundsCount - 1];
        } elseif ($number === 'START')
        {
            return $this->rounds[0];
        } elseif (intval($number) < 0 || intval($number) > ROUNDS)
        {
            throw new Exception('Invalid round number');
        } else
        {
            return $this->rounds[intval($number)];
        }
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
        $return = array();
        foreach ($lostShips->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $cost = $fighters->getCost();
                    $return[$idPlayer][$idFleet][$idFighters] = array($cost[0] * $fighters->getCount(), $cost[1] * $fighters->getCount());
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
    public function getDebris()
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
        if (isset($this->playerLostShips[$playersBefore->getId()]))
        {
            return $this->playerLostShips[$playersBefore->getId()];
        }

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
        $this->playerLostShips[$playersBefore->getId()] = $playersBefore_clone;
        return $playersBefore_clone;
    }
    public function getAfterBattleAttackers()
    {
        $players = $this->getResultAttackersFleetOnRound('END');
        $playersRepaired = $this->getAttackersRepaired();
        $players = DeepClonable::cloneIt($players);

        foreach ($playersRepaired->getIterator() as $idPlayer => $player)
        {
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $players->getPlayer($idPlayer)->getFleet($idFleet)->getFighters($idFighters)->increment($fighters->getCount());
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
            foreach ($player->getIterator() as $idFleet => $fleet)
            {
                foreach ($fleet->getIterator() as $idFighters => $fighters)
                {
                    $players->getPlayer($idPlayer)->getFleet($idFleet)->getFighters($idFighters)->increment($fighters->getCount());
                }
            }
        }
        return $players;
    }
}
