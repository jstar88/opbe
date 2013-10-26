## Installation

1. Download and upload to *ROOT_PATH/includes/libs/opbe/* all [OPBE files](https://github.com/jstar88/opbe/archive/master.zip) .
   You should see something like *ROOT_PATH/includes/libs/opbe/index.php*.
   (Remember to rename opbe-master to opbe)   
   Alternatively, you can open a terminal and do:

    ```
    cd ROOT_PATH/includes/libs/
    sudo git clone https://github.com/jstar88/opbe.git
    
    ```

2. Make sure of correct OPBE path,defined in the implementation file as:
    
    ```php
    require (ROOT_PATH . 'includes'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'opbe'.DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'includer.php');
    ```
3. Replace default 2Moons's *ROOT_PATH/includes/classes/missions/calculateAttack.php* with the one above 
    Alternatively, you can open a terminal and do:
    
    ```
    cd ROOT_PATH/includes/libs/opbe
    sudo cp implementations/2Moons/1_7_2/calculateAttack.php ROOT_PATH/includes/classes/missions/
    
    ```
    
4. Updating:
    You can use filezilla.
    Alternatively, you can open a terminal and do:
    ```
    cd ROOT_PATH/includes/libs/opbe
    sudo git pull
    sudo cp implementations/2Moons/1_7_2/calculateAttack.php ROOT_PATH/includes/classes/missions/
    
    ```
  
