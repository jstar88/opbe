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
class PlayerGroup extends DeepClonable
{

    protected $array = array();
    public $battleResult;
    private static $id_count = 0;
    private $id;

    public function __construct($players = array())
    {
        $this->id = ++self::$id_count;
        foreach ($players as $player)
        {
            $this->addPlayer($player);
        }
    }
    public function getId()
    {
        return $this->id;
    }
    public function decrement($idPlayer, $idFleet, $idFighters, $count)
    {
        if (!$this->existPlayer($idPlayer))
        {
            throw new Exception('Player with id : ' . $idPlayer . ' not exist');
        }
        $this->array[$idPlayer]->decrement($idFleet, $idFighters, $count);
        if ($this->array[$idPlayer]->isEmpty())
        {
            unset($this->array[$idPlayer]);
        }
    }
    public function getPlayer($id)
    {
        return $this->array[$id];
    }
    public function existPlayer($id)
    {
        return isset($this->array[$id]);
    }
    public function addPlayer(Player $player)
    {
        $this->array[$player->getId()] = $player;
    }
    public function isEmpty()
    {
        foreach ($this->array as $id => $player)
        {
            if (!$player->isEmpty())
            {
                return false;
            }
        }
        return true;
    }
    public function __toString()
    {
        $return = "";
        foreach ($this->array as $id => $player)
        {
            $return .= $player;
        }
        return $return;
    }
    public function getIterator()
    {
        return $this->array;
    }
    public function inflictDamage(FireManager $fire)
    {
        $physicShots = array();
        foreach ($this->array as $id => $player)
        {
            $ps = $player->inflictDamage($fire);
            $physicShots[$player->getId()] = $ps;
        }
        return $physicShots;
    }
    public function cleanShips()
    {
        $shipsCleaners = array();
        foreach ($this->array as $id => $player)
        {
            $sc = $player->cleanShips();
            $shipsCleaners[] = $sc;
            if ($player->isEmpty())
            {
                unset($this->array[$id]);
            }
        }
        return $shipsCleaners;
    }
    public function repairShields()
    {
        foreach ($this->array as $id => $player)
        {
            $player->repairShields();
        }
    }
    public function repairHull()
    {
        foreach ($this->array as $id => $player)
        {
            $player->repairHull();
        }
    }
    public function getEquivalentFleetContent()
    {
        $merged = new Fleet(-1);
        foreach ($this->array as $id => $player)
        {
            $merged->mergeFleet($player->getEquivalentFleetContent());
        }
        return $merged;
    }


}
