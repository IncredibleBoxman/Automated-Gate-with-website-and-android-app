This project was created by Michael Fulford and Yesenia Rodriguez.

A video demonstration can be found at:
https://www.youtube.com/watch?v=NtOuWH_mhIU

The website can be found at: https://testbuild322.000webhostapp.com/

The website is not being maintained or updated as it was created solely for this project. 
User: ben
pass: benpass 


QUICK SUMMARY:

Project uses raspberry pi (running Raspian) with Breadboad, LEDs jumpers and a PAAS 
cloud with a website.  

The LED's signify actuators and the jumpers signify switches.

A PAAS cloud is where the backend is handled with MySQL. 

The python automation sends and receives JSON requests to the database and updates
the database, which is then reflected on the android KIVY app and on the website.

First switch (male to male jumper) simulated if a car is waiting at gate.

Second switch simulated if the gate is closed

Third switch simulates if a car is passing the gate

First LED simulated gate opening

Second LED simulates gate closing

The app and automation send log messages to the database (opening, closing, alerts).
These log messages are viewable from the website.Charts of the transactions ocurring are 
also automatically created and viewable from the website. 

Key points of automation:

If car is in middle of gate and the gate is closing, the gate will stop closing and open.
It will also send alert to translog.

Automation is simulated by unplugging/plugging in the different switches. 


Key points of KIVY android app:

App allows for manual open/close of gates and also informs the user if the gate is 
currently opened or closed and if there is a car in the middle. Alert is sent if
user attempts to manually close gate with car in middle. 

App also allows for the acknowledgement of alerts.

Key points of website/backend:

Website creates charts and graphs reflected by the transaction log.

Transaction log identifies who has generated the log by either USER (Ben, Mary) or 
Auto (Automation).




