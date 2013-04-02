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
        return $this->array[$id];
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
