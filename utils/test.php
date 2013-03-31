<?php

include('DeepClonable.php');

class B extends DeepClonable{
    public $var1 = 'B';
    
}

class A extends DeepClonable
{
    public $var1;
    public $var2;
    public function __construct()
    {
        $this->var1 = array('primo'=>new B());
        $this->var2 = array('ciao','mondo');
    }
}

$a=new A();
$c = clone $a;
$a->var1['primo']->var1='A';
echo $c->var1['primo']->var1;
echo $c->var2[1];