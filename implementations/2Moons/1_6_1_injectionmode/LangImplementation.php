<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct()
    {
        global $LNG;
        $this->lang = $LNG; 
    }
    
    public function getShipName($id)
    {
        return $this->lang['tech_rc'][$id];
    }
    public function getAttackersAttackingDescr($amount, $damage)
    {
        return "The attacking fleet fires a total of " . $amount . ' times with the power of ' . $damage . " upon the defender.<br />";
    }
    public function getDefendersDefendingDescr($damage)
    {
        return "The defender's shields absorb " . $damage . " damage points.<br />";
    }
    public function getDefendersAttackingDescr($amount, $damage)
    {
        return "The defending fleet fires a total of " . $amount . ' times with the power of ' . $damage . " upon the attacker.<br />";
    }
    public function getAttackersDefendingDescr($damage)
    {
        return "The attacker's shields absorb " . $damage . " damage points.<br />";
    }
}

LangManager::getInstance()->setImplementation(new LangImplementation());
?>