<?php

require ("../RunnableTest.php");
class CrusherVsHunter extends RunnableTest
{
    public function getAttachers()
    {
        return new Fleet(array($this->getFighters(206, 50)));
    }
    public function getDefenders()
    {
        return new Fleet(array($this->getFighters(204, 400)));
    }
}
new CrusherVsHunter();

?>