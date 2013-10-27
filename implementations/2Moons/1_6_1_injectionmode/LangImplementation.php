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
}

LangManager::getInstance()->setImplementation(new LangImplementation());
?>