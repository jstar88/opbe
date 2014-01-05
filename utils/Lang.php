<?php

interface Lang
{
    public function getShipName($id);
    public function getAttackersAttackingDescr($amount, $damage);
    public function getDefendersDefendingDescr($damage);
    public function getDefendersAttackingDescr($amount, $damage);
    public function getAttackersDefendingDescr($damage);
}

?>