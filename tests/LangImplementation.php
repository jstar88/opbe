<?php

class LangImplementation implements Lang
{
    private $lang;
    public function __construct($name)
    {
        require("langs/$name.php");
        $this->lang = $lang;    
    }
    
    public function getShipName($id)
    {
        return $this->lang['tech_rc'][$id];
    }
}

?>