## Installation

1. Download and upload to *game_root/includes/libs/opbe/* all [OPBE files](https://github.com/jstar88/opbe/archive/master.zip) .
   You should see something like *game_root/includes/libs/opbe/index.php*.
   Alternatively, you can open a terminal and do:

    ```
    cd game_root/includes/libs/
    sudo git clone https://github.com/jstar88/opbe.git
    
    ```

2. Make sure that the constant *OPBEPATH* point to the correct OPBE path,defined in the implementation file as:
    
    ```php
    define('OPBEPATH', ROOT_PATH . 'includes/libs/opbe/');
    ```
3. Replace default 2Moons's *includes/classes/missions/calculateAttack.php* with the one above 
    Alternatively, you can open a terminal and do:
    
    ```
    sudo cp opbe/implementations/2Moons/1_6_1/calculateAttack.php game_root/includes/classes/missions/
    
    ```
    
4. Updating:
    You can use filezilla.
    Alternatively, you can open a terminal and do:
    ```
    cd game_root/includes/libs/opbe
    sudo git pull
    sudo cp implementations/2Moons/1_6_1/calculateAttack.php game_root/includes/classes/missions/
    
    ```
