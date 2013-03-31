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
class Ship extends Fighters
{
    public function getRepairProb()
    {
        return SHIP_REPAIR_PROB;
    }
}

?>