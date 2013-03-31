opbe
====

Ogame probabilistic battle engine

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

### Quick Start
Ok, seems so cool! How i can use it?  
You can check in **implementations** directory for your game version, read the installation.txt file.  
Be sure you had read the *license* with respect for the author.


---

### License

![license](http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png)  
This work is licensed under the Creative Commons Attribuzione - Non commerciale - Condividi allo stesso modo 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/3.0/.

