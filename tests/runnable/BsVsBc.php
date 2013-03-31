<?php

require ("../RunnableTest.php");
class BsVsBc extends RunnableTest
{
    public function getAttachers()
    {
        return new Fleet(array($this->getFighters(207, 50)));
    }
    public function getDefenders()
    {
        return new Fleet(array($this->getFighters(215, 50)));
    }
}
new BsVsBc();

?>