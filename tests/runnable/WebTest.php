<?php

require ("../RunnableTest.php");
class WebTest extends RunnableTest
{
    public function getAttachers()
    {
        return $this->buildPlayerGroup($_POST["attacker_tech"], $_POST["attacker_fleet"]);
    }
    public function getDefenders()
    {
        return $this->buildPlayerGroup($_POST["defender_tech"], $_POST["defender_fleet"]);
    }

    private function buildPlayerGroup($tech, $fleets)
    {
        $playerObj = new Player(1);
        foreach ($fleets as $idFleet => $fleet)
        {
            $fleetObj = new Fleet($idFleet);
            foreach ($fleet as $id => $count)
            {
                $count = intval($count);
                $id = intval($id);
                if ($count > 0 && $id > 0)
                {
                    $fleetObj->add($this->getFighters($id, $count));
                }
            }
            if (!$fleetObj->isEmpty())
            {
                $fleetObj->setTech($tech[$idFleet]['weapons'], $tech[$idFleet]['shields'], $tech[$idFleet]['armour']);
                $playerObj->addFleet($fleetObj);
            }
        }
        if ($playerObj->isEmpty())
        {
            die("<meta http-equiv=\"refresh\" content=2;\"WebTest.php\">There should be at least an attacker and defender");
        }
        $playerGroupObj = new PlayerGroup();
        $playerGroupObj->addPlayer($playerObj);
        return $playerGroupObj;
    }
}

if ($_GET['good'])
{
    session_start();
    if (!isset($_SESSION['vote']))
    {
        $_SESSION['vote'] = true;
        $count = file_get_contents('good.txt');
        $count++;
        file_put_contents('good.txt', $count);
    }
    session_write_close();
}
elseif ($_GET['bad'])
{
    session_start();
    if (!isset($_SESSION['vote']))
    {
        $_SESSION['vote'] = true;
        $count = file_get_contents('bad.txt');
        $count++;
        file_put_contents('bad.txt', $count);
    }
    session_write_close();
}
if ($_POST)
{

    session_start();
    if (!isset($_SESSION['time']))
    {
        $_SESSION['time'] = time();
    }
    else
    {
        if (time() - $_SESSION['time'] < 3)
        {
            die('Sorry,to prevent malicious usage you can only execute one simulation each 3 seconds');
        }
        $_SESSION['time'] = time();
    }
    session_write_close();
    $count = file_get_contents('count.txt');
    $count++;
    file_put_contents('count.txt', $count);
    new WebTest($_POST['debug'] === 'secretxxx');
}
else
{
    $bad = file_get_contents('bad.txt');
    $good = file_get_contents('good.txt');
    $count = floor(file_get_contents('count.txt'));
    require ('WebTestGui.html');

}

?>