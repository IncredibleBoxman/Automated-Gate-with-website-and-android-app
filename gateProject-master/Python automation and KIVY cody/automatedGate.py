#!/usr/bin/python

import requests #import JSONRequests library

import time #import time library for sleep function

import datetime #import datetime library for timestamp

import RPi.GPIO as GPIO #import GPIO library



GPIO.setmode(GPIO.BCM) #set the pins according to BCM scheme

GPIO.setup(5,GPIO.OUT) #configure BCM Pin #5 as OUTPUT

GPIO.setup(6,GPIO.OUT) #configure BCM Pin #6 as OUTPUT

GPIO.setup(17,GPIO.IN) #configure BCM Pin #17 as INPUT

GPIO.setup(27,GPIO.IN) #configure BCM Pin #27 as INPUT

GPIO.setup(22,GPIO.IN) #configure BCM Pin #22 as INPUT

GPIO.output(5, GPIO.LOW)
GPIO.output(6, GPIO.LOW)
#Creating two booleans to check when a car has passed
carPassing = False
carPassed = False


i=0; n=5; delay=5 #limit number of tries to 5 (initially set it to 1 for debugging)

while i<n:
	#If a car is at the gate, and the gate is closed open gate
	if GPIO.input(17) == 1 & GPIO.input(27) == 1:
		print "Opening Gate: "
		GPIO.output(5,GPIO.HIGH)
		#This msgData is being used to provide login information to provide quick messages to translog
		msgData = {'username':'ben', 'password':'benpass'}
		#This sends canned message that represents "open" to translog
		requests.post("https://testbuild322.000webhostapp.com/scripts/auto_open_msg.php", json=msgData)
		#Delays the next check. Might need to make it 10 seconds in order to avoid warning message. 
		time.sleep(5)
		while True:
			#Checks if gate is still closed and sends a message before delaying and checking again. 
			while GPIO.input(27) == 1:
				#This would be a good "alert acknowledge for the app"
				print "Warning Gate still reading closed"
				time.sleep(5)
			while True:
				#By writing the while statement this way
				#Forces everything to wait UNTIL switch 3 has been turned on
				if GPIO.input(22) ==1:
					carPassing = True
					time.sleep(5)
					break
			#Now that switch 3 has been triggered on will continously check
			#until switch 3 is turned off
			if GPIO.input(22) == 0:
				carPassed = True
			#If both bools are true, then breaks the loop. 
			if carPassing & carPassed:
				break
	#Stops opening gate as the "Car" has passed
	GPIO.output(5, GPIO.LOW)
	#If gate is not closed, start closing
	if GPIO.input(27) == 0:
		#Print closing gate and send auto closing msg to translog
		print "Closing Gate: "
		requests.post("https://testbuild322.000webhostapp.com/scripts/auto_close_msg.php", json=msgData)
		#Begin closing gate
		while GPIO.input(27) == 0:
			GPIO.output(6, GPIO.HIGH)
			#Continuously checks if a car is in front of gate
			if GPIO.input(22) == 1:
			#If a car is in front of gate while closing, stops closing and breaks the loop
				print "Car in front of closing gate"
				GPIO.output(6, GPIO.LOW)
				break
			time.sleep(5)
	LED1=GPIO.input(5) #read what BCM Pin #5 is set to (LED1)

	LED2=GPIO.input(6) #read what BCM Pin #6 is set to (LED2)

	SW1=GPIO.input(17) #read the status of BCM Pin #17 (SW1)

	SW2=GPIO.input(27) #read the status of BCM Pin #27 (SW2)

	SW3=GPIO.input(22) #read the status of BCM Pin #22 (SW3)


	#This is the attached Android section
	#Automation will always take precedence.
	#At this time I do not know how to force android to cancel automation
	data = {'username':'ben', 'password':'benpass', 'SW1':SW1, 'SW2':SW2, 'SW3':SW3, 'LED1': LED1, 'LED2':LED2}    #json request payload

	print data

	res = requests.post("https://testbuild322.000webhostapp.com/scripts/sync_rpi_data2.php", json=data)

	#in case of errors (especially, syntax) , you may want to print res.text and comment out the statements below
	r = res.json()

	ts = datetime.datetime.now() #get the time stamp

	print "==============Server Response at " + str(ts) + "=============="

	if r['success']==1:

		print "+++++Server request successful: "

		#check LED1 and update if necessary

		if LED1!=r['LED1']:

			print "Opening gate as requested by the server"

			if r['LED1']==1:

				GPIO.output(5,GPIO.HIGH)

			else: GPIO.output(5,GPIO.LOW)

        	#check LED2 and update if necessary

        	if LED2!=r['LED2']:

            		print "Closing gate as requested by the server"

            		if r['LED2']==1:

                		GPIO.output(6,GPIO.HIGH)

            		else: GPIO.output(6,GPIO.LOW)

        	print "Opening Gate: " + str(r['LED1'])

        	print "Closing Gate: " + str(r['LED2'])

        	print "Vehicle at the Gate: " + str(r['SW1'])

        	print "Gate is Closed: " + str(r['SW2'])

        	print "Vehicle in the Path of gate " + str(r['SW3'])

	else: print ">>>>> Server request failed - Error #" + str(r['error'])

    	time.sleep(delay) #wait for delay seconds before sending another request

    	i+=1
		

GPIO.cleanup()
