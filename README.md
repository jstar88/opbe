opbe
====

Ogame Probabilistic Battle Engine  
live: http://opbe.webnet32.com

### Introduction
OPBE is the first(and only) battle engine in the world that use Probability theory:  
battles is processed very very fast and required few memory resources.  
Also memory and cpu usage are O(1), this means they are COSTANTS and independent of the ships amount.    

---

### Accuracy
One of main concept used in this battle engine is the **expected value**: instead calculate each ship case, opbe 
creates an estimation of their behavior. 
This estimation is improved analyzing behavior for infinite simulations, so 
to test opbe accuracy you have to set speedsim(dragosim)'s battles amount to a big number, such as 3k.  


---

### Quick start
Ok, seems so cool! How i can use it?  
You can check in **implementations** directory for your game version, read the installation.txt file.  
Be sure you had read the *license* with respect for the author.


---
### Dev guide
The system organization is like :
* PlayerGroup
   * Player1
   * Player2
      * Fleet1
      * Fleet2
         * Fighters1
         * Fighters2

So in a PlayerGroup there are differents Player,  
each Player have differents Fleets,  
each Fleet have differents Figthers.  

An easy way to display them:
```php   
    $fleetObj = new Fleet($idFleet);
    $fleetObj->add($this->getFighters($id, $count));
    
    $playerObj = new Player($idPlayer);
    $playerObj->addFleet($fleetObj);
    
    $playerGroupObj = new PlayerGroup();
    $playerGroupObj->addPlayer($playerObj);
```

#### Fighters

Fighters are the smallest object in the system: it rappresent a group of specific object type able to fight.
For some reason, opbe need to categorize it in two type extending Fighters:
* Defense
* Ship

Don't care about this fact because you should use this automatic code:

```php
   
   public function getFighters($id, $count)
    {
        global $CombatCaps, $pricelist;
        $rf = $CombatCaps[$id]['sd'];
        $shield = $CombatCaps[$id]['shield'];
        $cost = array($pricelist[$id]['metal'], $pricelist[$id]['crystal']);
        $power = $CombatCaps[$id]['attack'];
        if ($id <= 217)
        {
            return new Ship($id, $count, $rf, $shield, $cost, $power);
        }
        return new Defense($id, $count, $rf, $shield, $cost, $power);
    }
   
```

---

### License

![license](http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png)  
This work is licensed under the Creative Commons Attribuzione - Non commerciale - Condividi allo stesso modo 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/3.0/.

