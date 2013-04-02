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
 *
 *
 * Fire
 *  
 * This class rappresent the fire shotted by attackers to defenders or viceversa.
 * Using probabilistic theory, this class will help you in RF(Rapid Fire) calculation with O(1) time and memory functions.
 * Sometime i think that SpeedSim's RF calculation is bugged, so you can choose if return its result or not setting "SPEEDSIM" constant to true/false.
 */
class Fire
{
    private $attackers;
    private $defenders;
    const SPEEDSIM = true;
    const RAPIDFIRE = true;

    private $shots = null;
    private $power = null;

    /**
     * Fire::__construct()
     * 
     * @param Fleet $attackers
     * @param Fleet $defenders
     * @param bool $attacking
     * @return
     */
     public function __construct(Fighters $attackers, Fleet $defenders)
    //public function __construct(Fleet $attackers, Fleet $defenders)
    {
        $this->attackers = $attackers;
        $this->defenders = $defenders;
    }
    public function getPower()
    {
        return $this->attackers->getPower();
    }
    public function getId()
    {
        return $this->attackers->getId();
    }
    //----------- SENDED FIRE -------------
    /**
     * Fire::getAttacherTotalFire()
     * 
     * @return
     */
    public function getAttacherTotalFire()
    {
        if ($this->power == null)
        {
            $this->calculateTotal();
        }
        return $this->power;
    }
    /**
     * Fire::getAttackerTotalShots()
     * 
     * @return
     */
    public function getAttackerTotalShots()
    {
        if ($this->shots == null)
        {
            $this->calculateTotal();
        }
        return $this->shots;
    }
    /**
     * Fire::calculateTotal()
     * 
     * @return
     */
    private function calculateTotal()
    {
        $this->shots = 0;
        $this->power = 0;
        if (self::RAPIDFIRE)
        {
            $this->calculateRf();            
        }
        if (!self::SPEEDSIM || !self::RAPIDFIRE)
        {
            //$this->shots += $this->attackers->getTotalCount();
            $this->shots += $this->attackers->getCount();
            $this->power += $this->getNormalPower();
        }
    }
    /**
     * Fire::calculateRf()
     * 
     * @return
     */
    private function calculateRf()
    {
#        foreach ($this->attackers->getIterator() as $fighters_A)
#        {
#            $tmpshots = round($this->getShotsFromOneAttackerShipOfType($fighters_A) * $fighters_A->getCount());
#            if (self::SPEEDSIM && $tmpshots == 0)
#            {
#                $tmpshots = $fighters_A->getCount();
#            }
#            $this->power += $tmpshots * $fighters_A->getPower();
#            $this->shots += $tmpshots;
#        }
        $tmpshots = round($this->getShotsFromOneAttackerShipOfType($this->attackers ) * $this->attackers->getCount());
           if (self::SPEEDSIM && $tmpshots == 0)
            {
                $tmpshots = $this->attackers->getCount();
            }
            $this->power += $tmpshots * $this->attackers->getPower();
           $this->shots += $tmpshots;
    }
    /**
     * Fire::getShotsFromOneAttackerShipOfType()
     * 
     * @param mixed $fighters_A
     * @return
     */
    private function getShotsFromOneAttackerShipOfType(Fighters $fighters_A)
    {
        $p = $this->getProbabilityToShotAgainForAttackerShipOfType($fighters_A);
        return ($p != 1) ? 1 / (1 - $p) : 0;
    }
    /**
     * Fire::getProbabilityToShotAgainForAttackerShipOfType()
     * 
     * @param mixed $fighters_A
     * @return
     */
    private function getProbabilityToShotAgainForAttackerShipOfType(Fighters $fighters_A)
    {
        $p = 0;
        foreach ($this->defenders->getIterator() as $fighters_D)
        {
            $RF = $fighters_A->getRfTo($fighters_D);
            if (!self::SPEEDSIM)
            {
                $RF = max(0, $RF - 1);
            }
            $probabilityToShotAgain = ($RF != 0) ? ($RF - 1) / $RF : 0;
            $probabilityToHitThisType = $fighters_D->getCount() / $this->defenders->getTotalCount();
            $p += $probabilityToShotAgain * $probabilityToHitThisType;
        }
        return $p;
    }
    /**
     * Fire::getNormalPower()
     * 
     * @return
     */
    private function getNormalPower()
    {#
#        $power = 0;
        #foreach ($this->attackers->getIterator() as $attacker)
#        {
#            $power += $attacker->getCount() * $attacker->getPower();
#        }
        #return $power;
        return $this->attackers->getCount() * $this->attackers->getPower();
    }
    //------- INCOMING FIRE------------

    public function getShotsFiredByAttackerTypeToDefenderType(Fighters $fighters_A, Fighters $fighters_D, $real = false)
    {
        /*$num = $this->getShotsFiredByAllToDefenderType($fighters_D) * $fighters_A->getCount();
        $denum = $this->attackers->getTotalCount();
        return $this->divide($num,$denum,$real,"shots","rest");*/
        $first = $this->getShotsFiredByAttackerToOne($fighters_A);
        $second = new Number($fighters_D->getCount());
        return Math::multiple($first, $second, $real);
    }
    public function getShotsFiredByAttackerToOne(Fighters $fighters_A, $real = false)
    {
        $num = $this->getShotsFiredByAttackerToAll($fighters_A);
        $denum = new Number($this->defenders->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    public function getShotsFiredByAllToDefenderType(Fighters $fighters_D, $real = false)
    {
        /*$num = $this->getAttackerTotalShots() * $fighters_D->getCount();
        $denum = $this->defenders->getTotalCount();
        return $this->divide($num,$denum,$real,"shots","rest");*/
        $first = $this->getShotsFiredByAllToOne();
        $second = new Number($fighters_D->getCount());
        return Math::multiple($first, $second, $real);
    }
    public function getShotsFiredByAttackerToAll(Fighters $fighters_A, $real = false)
    {
        $num = new Number($this->getAttackerTotalShots() * $fighters_A->getCount());
        $denum = new Number($this->attackers->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    public function getShotsFiredByAllToOne($real = false)
    {
        $num = new Number($this->getAttackerTotalShots());
        $denum = new Number($this->defenders->getTotalCount());
        return Math::divide($num, $denum, $real);
    }
    /**
     * Fire::__toString()
     * 
     * @return
     */
    public function __toString()
    {
        #global $resource;
#        $shots = $this->getAttackerTotalShots();
#        $power = $this->getAttacherTotalFire();
#        $iter = $this->attackers->getIterator();
#        $page = "<center><table bgcolor='#ADC9F4' border='1' ><body><tr><tr><td colspan='" . count($iter) . "'><center><font color='red'>Attackers</font></center></td></tr>";
#        foreach ($iter as $attacher)
#            $page .= "<td>" . $resource[$attacher->getId()] . "</td>";
#        $page .= "</tr><tr>";
#        foreach ($iter as $attacher)
#            $page .= "<td><center>" . $attacher->getCount() . "</center></td>";
#        $iter = $this->defenders->getIterator();
#        $page .= "</tr></body></table><br><table bgcolor='#ADC9F4' border='1'><body><tr><td colspan='" . count($iter) . "'><center><font color='red'>Defenders</font></center></td></tr></tr>";
#        foreach ($iter as $defender)
#            $page .= "<td>" . $resource[$defender->getId()] . "</td>";
#        $page .= "<tr>";
#        foreach ($iter as $defender)
#            $page .= "<td><center>" . $defender->getCount() . "</center></td>";
#        $page .= "</tr></body></table><br>";
#        $page .= "The attacking fleet fires a total of $shots times with the power of $power upon the defenders.<br>";
#        $page .= "</center>";
#        return $page;
    return $this->getAttacherTotalFire().'';
    }

}
