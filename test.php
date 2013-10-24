<?php

define("OPBEPATH", "");
require "utils/includer.php";

$types = array(new Fighters(204, 100, 10, 10, array(), 10));
$fleets = array(new Fleet(1, $types));
$players = array(new Player(1, $fleets), new Player(2, $fleets));
$plg = new PlayerGroup($players);

echo $plg; // output is correct
echo "<br>------<br>";
 $plg->getEquivalentFleetContent();
echo "-------<br>";
echo $plg; //ships are doubled without this fix https://github.com/jstar88/opbe/commit/12d127299fabdb252f98f12221c66862fe135934

?>