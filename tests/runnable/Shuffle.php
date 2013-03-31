<?php
	require ("../RunnableTest.php");
class Shuffle extends RunnableTest
{
    public function getAttachers()
    {
        return new Fleet(array(
        $this->getFighters(206, 50),
        $this->getFighters(207, 50),
        $this->getFighters(204, 150)
        ));
    }
    public function getDefenders()
    {
        return new Fleet(array(
        $this->getFighters(210, 150),
        $this->getFighters(215, 50),
        $this->getFighters(207, 20)
        ));
    }
}
new Shuffle();
?>