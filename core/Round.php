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
