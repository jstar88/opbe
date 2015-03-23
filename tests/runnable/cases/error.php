<?php

require ("../../RunnableTest.php");
class Fazi extends RunnableTest
{
    public function getAttachers()
    {
        $ships = array();
        $ships[] = $this->getShipType(204, 3000);
        $fleet = new Fleet(1, $ships);
        $player = new Player(1, array($fleet));
        return new PlayerGroup(array($player));
    }
    public function getDefenders()
    {
        $ships = array();
        $ships[] = $this->getShipType(203, 351);
        $ships[] = $this->getShipType(204, 388);
        $ships[] = $this->getShipType(205, 139);
        $ships[] = $this->getShipType(206, 745);
        $ships[] = $this->getShipType(207, 634);
        $ships[] = $this->getShipType(209, 106);
        $ships[] = $this->getShipType(210, 200);
        $fleet = new Fleet(2, $ships);
        $player = new Player(2, array($fleet),1,1,1);
        return new PlayerGroup(array($player));
    }
}
new Fazi();