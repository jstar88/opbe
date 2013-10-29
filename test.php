 <?php

define("PATH", "");
require "utils/includer.php";

$types = array(new Fighters(204, 100, 10, 10, array(), 10));
$fleets = array(new Fleet(1, $types));
$players = array(new Player(1, $fleets), new Player(2, $fleets));
$plg = new PlayerGroup($players);

echo $plg; // output is correct
$plg->getEquivalentFleetContent();
echo $plg; //ships are doubled !!!

?> 
