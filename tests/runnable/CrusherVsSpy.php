<?php

require ("../RunnableTest.php");
class CrusherVsSpy extends RunnableTest
{
    public function getAttachers()
    {
        return new Fleet(array($this->getFighters(206, 150)));
    }
    public function getDefenders()
    {
        return new Fleet(array($this->getFighters(210, 1250)));
    }
}
new CrusherVsSpy();

?>