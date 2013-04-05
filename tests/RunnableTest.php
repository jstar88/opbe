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
require ("../../utils/DeepClonable.php");
require ("../../utils/Math.php");
require ("../../utils/Number.php");
require ("../../utils/Events.php");
require ("../../models/Type.php");
require ("../../models/Fighters.php");
require ("../../models/Fleet.php");
require ("../../models/Player.php");
require ("../../models/PlayerGroup.php");
include ("../../models/Defense.php");
include ("../../models/Ship.php");
require ("../../combatObject/Fire.php");
require ("../../combatObject/PhysicShot.php");
require ("../../combatObject/ShipsCleaner.php");
require ("../../combatObject/FireManager.php");
require ("../../core/Battle.php");
require ("../../core/BattleReport.php");
require ("../../core/Round.php");
require ("vars.php");
require ("../../constants/battle_constants.php");

class RunnableTest
{
    public function __construct($debug = false)
    {
        global $resource;
        try
        {
            set_error_handler(array('RunnableTest', "myErrorHandler"));
            $attackers = $this->getAttachers();
            $defenders = $this->getDefenders();
            $memory1 = memory_get_usage();
            $micro1 = microtime();

            $engine = new Battle($attackers, $defenders);
            $engine->startBattle($debug);

            $micro1 = microtime() - $micro1;
            $memory1 = memory_get_usage() - $memory1;

            echo $engine->getReport()->toString($resource);

            $micro1 = round(1000 * $micro1, 2);
            $memory1 = round($memory1 / 1000);
            echo <<< EOT
<br>______________________________________________<br>
Battle calculated in <font color=blue>$micro1 ms</font>.<br>
Memory used: <font color=blue>$memory1 KB</font><br>
_______________________________________________<br>
EOT;
        }
        catch (exception $e)
        {
            self::save($e);
        }

    }
    public function getFighters($id, $count)
    {
        global $CombatCaps, $pricelist;
        $rf = $CombatCaps[$id]['sd'];
        $shield = $CombatCaps[$id]['shield'];
        $cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);
        $power = $CombatCaps[$id]['attack'];
        if ($id <= 217)
        {
            return new Ship($id, $count, $rf, $shield, $cost, $power);
        }
        return new Defense($id, $count, $rf, $shield, $cost, $power);
    }
    public function getAttachers()
    {
        //light_fighter
        $id = 204;
        $count = 100;
        $a4 = $this->getFighters($id, $count);
        //cruiser
        $id = 206;
        $count = 150;
        $a1 = $this->getFighters($id, $count);
        //battle_ship
        $id = 207;
        $count = 50;
        $a2 = $this->getFighters($id, $count);
        //destroyer
        $id = 213;
        $count = 50;
        $a3 = $this->getFighters($id, $count);

        $fleet1 = new Fleet(array($a2));
        $player1 = new Player(1, array($fleet1), 0, 0, 0);
        return new PlayerGroup(array($player1));
    }
    public function getDefenders()
    {
        //light_fighter
        $id = 204;
        $count = 100;
        $d1 = $this->getFighters($id, $count);
        //probe
        $id = 210;
        $count = 300;
        $d2 = $this->getFighters($id, $count);
        //battle_cruiser
        $id = 215;
        $count = 50;
        $d3 = $this->getFighters($id, $count);

        $fleet2 = new Fleet(array($d3));
        $player2 = new Player(2, array($fleet2), 0, 0, 0);
        return new PlayerGroup(array($player2));
    }
    public static function myErrorHandler($errno, $errstr, $errfile, $errline)
    {
        $error = '';
        switch ($errno)
        {
            case E_USER_ERROR:
                $error .= "ERROR [$errno] $errstr<br />";
                break;

            case E_USER_WARNING:
                $error .= "WARNING [$errno] $errstr<br />";
                break;

            case E_USER_NOTICE:
                $error .= "NOTICE [$errno] $errstr<br />";
                break;

            default:
                $error .= "Unknown error type: [$errno] $errstr<br />";
                break;
        }
        $error .= "Error on line $errline in file $errfile";
        $error .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />";
        self::save($error);
        /* Don't execute PHP internal error handler */
        return true;

    }
    private static function save($other)
    {
        $time = date('l jS \of F Y h:i:s A');
        $post = '$_POST =' . var_export($_POST);
        $get = '$_GET =' . var_export($_GET);
        $output = ob_get_clean();
        $old = file_get_contents('errors.txt');
        $separate = "---------------------------------";
        file_put_contents('errors.txt', $old . PHP_EOL . $separate . PHP_EOL . $time . PHP_EOL . $this->br2nl($other) . PHP_EOL . $post . PHP_EOL . $get . PHP_EOL . $this->br2nl($output));
        die('An error occurred, we will resolve it soon as possible');
    }
    private static function br2nl($text)
    {
        return preg_replace('/<br\\\\s*?\\/??>/i', PHP_EOL, $text);
    }
}

?>