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
class Defense extends Fighters
{
    public function __construct($id, $count, $rf, $shield, array $cost, $power, $w = 0, $s = 0, $a = 0)
    {
        parent::__construct($id, $count, $rf, $shield, $cost, $power, $w, $s, $a);
    }
    public function getRepairProb()
    {
        return DEFENSE_REPAIR_PROB;
    }

}

?>