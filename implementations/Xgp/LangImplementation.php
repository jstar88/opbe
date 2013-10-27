<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct()
    {
        global $lang;
        if(empty($lang))
        {
            includeLang ( 'INGAME' );
        }
        $this->lang = $lang;    
    }
    
    public function getShipName($id)
    {
        return $this->lang['tech_rc'][$id];
    }
}

LangManager::getInstance()->setImplementation(new LangImplementation());
?>