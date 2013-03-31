<?php
	$d=array_fill(1, 10, 0);
    for($i=0;$i<100000;$i++)
    {
        $d[mt_rand(1,10)]++;
    }
    foreach($d as $key => $value)
     echo $value."<br>";

?>