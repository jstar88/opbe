<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct($name)
    {
        require (OPBEPATH . "tests/runnable/langs/$name.php");
        $this->lang = $lang;
    }

    public function getShipName($id)
    {
        return isset($this->lang['tech_rc'][$id]) ? $this->lang['tech_rc'][$id] : $id . ' <font color=red>* no lang found</font>';
    }

    public function getAttackersAttackingDescr($amount, $damage)
    {
        return $this->lang['fleet_attack_1'] . ' ' . $damage . " " . $this->lang['damage'] . " with $amount shots ";
    }
    public function getDefendersDefendingDescr($damage)
    {
        return $this->lang['fleet_attack_2'] . $damage . ' ' . $this->lang['damage'];;
    }
    public function getDefendersAttackingDescr($amount, $damage)
    {
        return $this->lang['fleet_defs_1'] . ' ' . $damage . " " . $this->lang['damage'] . " with $amount shots ";
    }
    public function getAttackersDefendingDescr($damage)
    {
        return $this->lang['fleet_defs_2'] . $damage . ' ' . $this->lang['damage'];
    }
}

?>