Replace all content of function includes/classes/class.FlyingFleetHandler.php::missionCaseAttack() with 

   ```php
      $opbePath = XGP_ROOT.'includes/libs/opbe/'; // XGP 2.10.x
      //$opbePath = $xgp_root.'includes/libs/opbe/'; // XGP 2.9.x
      require($opbePath.'implementations/missionCaseAttack.php'); 
   ```

where $opbePath is the path where you uploaded the battle engine pack. 
