<?php
/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version alpha(2013-2-4)
 * @link https://github.com/jstar88/opbe
 */
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
        $playerObj->setTech($tech['weapons'], $tech['shields'], $tech['armour']);
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
    new WebTest($_POST['debug'] === 'xxx');
}
else
{
    $bad = file_get_contents('bad.txt');
    $good = file_get_contents('good.txt');
    $count = floor(file_get_contents('count.txt'));
    require ('WebTestGui.html');

}

?>