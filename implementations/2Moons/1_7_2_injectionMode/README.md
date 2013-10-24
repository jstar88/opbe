## InjectionMode
Default installatin mode move your implementation file to replace old calculateAttack.However, in this way, your project has 2 same files
and expecially each time OPBE lib is updated you need to copy that implementation.     
Instead, using injectionMode the only used OPBE's implementation stay inside OPBE.  

## Installation

1. Download and upload all [OPBE files](https://github.com/jstar88/opbe/archive/master.zip) where you prefer.
   For example,if you upload to *ROOT_PATH/includes/libs/*, you should see something like *ROOT_PATH/includes/libs/opbe-master/index.php*.  
   Alternatively, you can open a terminal and do:

    ```
    cd ROOT_PATH/includes/libs/
    sudo git clone https://github.com/jstar88/opbe.git
    
    ```

2. Replace all code inside includes/classes/missions/calculateAttack.php with:
    
    ```php
    <?php
        require( ROOT_PATH . 'includes/libs/opbe-master/implementations/2Moons/1_7_2_injectionmode/calculateAttack.php' );
    ?>
    ```
3. Updating:
    You can use filezilla to upload opbe lib.   
    Alternatively, you can open a terminal and do:
    ```
    cd ROOT_PATH/includes/libs/opbe-master
    sudo git pull
    
    ```
    