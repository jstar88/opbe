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
class FireManager extends DeepClonable
{
    protected $array = array();
    private $position = 0;
    public function add(Fire $fire)
    {
        $this->array[] = $fire;
    }
    public function getAttackerTotalShots()
    {
        
        $tmp=0;
        foreach($this->array as $id => $fire)
        {
            $tmp+= $fire->getAttackerTotalShots();    
        }
        return $tmp;
    }
    public function getAttacherTotalFire()
    {
        
        $tmp=0;
        foreach($this->array as $id => $fire)
        {
            $tmp+= $fire->getAttacherTotalFire();    
        }
        return $tmp;
    }
    public function getIterator()
    {
        return $this->array;
    }
    
}
