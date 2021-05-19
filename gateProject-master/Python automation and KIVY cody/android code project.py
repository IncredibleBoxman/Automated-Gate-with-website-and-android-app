
from kivy.app import App
from kivy.uix.switch import Switch
from kivy.uix.gridlayout import GridLayout
from kivy.uix.label import Label
from kivy.clock import Clock
from functools import partial
from kivy.uix.popup import Popup
from kivy.uix.button import Button
from datetime import datetime
from datetime import timedelta
import time
import requests


class SwitchContainer(GridLayout): #Create a class that uses Gridlayout module
    def __init__(self, **kwargs):
        super(SwitchContainer,self).__init__(**kwargs)
        self.cols = 2
        

        self.updateLabel = Label(text = 'Last updated: ')
        self.add_widget(self.updateLabel)

        self.updateLabel2 = Label()
        self.add_widget(self.updateLabel2)
        

        self.add_widget(Label(text="Open gate: ")) #create a label for LED1 "Open Gate"
        self.led1 = Switch(active=False)#create a SwitchCompat for LED1(default to OFF)
        self.add_widget(self.led1) #add the created SwitchCompat to the screen
        self.led1.bind(active=switch_callback1)

        self.add_widget(Label(text="Close gate: ")) #create a label for LED2 "Close Gate"
        self.led2 = Switch(active=False)#create a SwitchCompat for LED1(default to OFF)
        self.add_widget(self.led2) #add the created SwitchCompat to the screen
        self.led2.bind(active=switch_callback2)



        self.add_widget(Label(text="Vehicle at gate: "))   #create a label for SW1
        self.sw1 = Switch(active=False) #create a SwitchCompat for SW1(default to OFF)
        self.add_widget(self.sw1)   #add the created SwitchCompat to the screen
        self.sw1.disabled = True    #make SW1unclickable on the app

        self.add_widget(Label(text="Gate is closed "))   #create a label for SW2
        self.sw2 = Switch(active=False) #create a SwitchCompat for SW2(default to OFF)
        self.add_widget(self.sw2)   #add the created SwitchCompat to the screen
        self.sw2.disabled = True    #make SW2unclickable on the app

        self.add_widget(Label(text="Vehicle crossing gate "))   #create a label for SW3
        self.sw3 = Switch(active=False) #create a SwitchCompat for SW3(default to OFF)
        self.add_widget(self.sw3)   #add the created SwitchCompat to the screen
        self.sw3.disabled = True    #make SW3unclickable on the app

        self.alertLabel = Label(text = 'Alert not detected')
        self.add_widget(self.alertLabel)

        self.alertLabel2 = Label(text = '')
        self.add_widget (self.alertLabel2)

        self.add_widget(Label(text = "Acknowledge"))
        self.sw4 = Switch(active=False)
        self.add_widget(self.sw4)
        self.sw4.bind(active=switch_callback3) 
        self.sw4.disabled = True
        
        #schedule the JSonRequest function to trigger every 5 seconds to read/write databases 
        event = Clock.schedule_interval(partial(self.JSONrequest), 10)
        event = Clock.schedule_interval(partial(self.JSONrequest2), 10)
    def JSONrequest(self, *largs):
        self.now = datetime.now()
        self.updateLabel2.text = str(self.now)
        
        
        if (self.led1.active == True & self.led2.active == True):
            self.led1.active = False
            self.led2.active = False
            call_popup()
        if (self.led1.active == True): #Get status of led1, convert to an integer and disable LED2 if active
            LED1 = 1
            self.led2.disabled = True
        else:
            LED1 = 0
            self.led2.disabled = False
        if (self.led2.active == True): #Get status of led2, convert to an integer and disable LED1 if active
            LED2 = 1
            self.led1.disabled = True
        else:
            LED2 = 0
            self.led1.disabled = False
        if (self.sw1.active == True): #Get status of sw1, convert to an integer
            SW1 = 1
        else:
            SW1 = 0
        if (self.sw2.active == True): #Get status of sw1, convert to an integer
            SW2 = 1
        else:
            SW2 = 0
        if (self.sw3.active == True): #Get status of sw1, convert to an integer
            SW3 = 1
        else:
            SW3 = 0
        #below is the json request payload, the request and the response

        data = {'username': 'ben','password':'benpass', 'SW1':SW1, 'SW2':SW2, 'SW3':SW3, 'LED1': LED1, 'LED2':LED2}    #json request payload
        res = requests.post("https://testbuild322.000webhostapp.com/scripts/sync_app_data2.php", json=data)
        print (res.text)
        r = res.json()  #json response

        if SW1 != r['SW1']: #check the received value of SW1 & change it on the App if there is a mismatch
            print("Changing SW1 status to the value in the database.")
            if self.sw1.active == True:
                self.sw1.active = False
            else:
                self.sw1.active = True
        elif SW2 != r['SW2']: #check value of SW2 and change it on the app if there is a mismatch
            print("Changing SW2 status to the value in the database.")
            if self.sw2.active ==True:
                self.sw2.active = False
            else:
                self.sw2.active = True
        elif SW3 != r['SW3']: #check value of SW3 and change it on the app if there is a mismatch
            print("Changing SW3 status to the value in the database.")
            if self.sw3.active ==True:
                self.sw3.active = False
            else:
                self.sw3.active = True
        else: 
            return
    
    
    def JSONrequest2(self, *args):
        
        #Alert json request payload
        alertData = {'username': 'ben', 'password':'benpass'}
        res2 = requests.post("https://testbuild322.000webhostapp.com/scripts/app_alert.php", json=alertData)
        print (res2.text)
        rTwo = res2.json()  #json response
        #If we find ack with 0 then update labels with the information and allow acknowledge button
        if rTwo['ACK'] ==0:
            self.alertLabel.text = 'Alert detected '
            self.alertLabel2.text = str(self.now) + ' MSGID ' +str(rTwo['MSGID'])
            self.sw4.disabled = False
        #if we don't, then update labels to default case and deactivate/disable ackowledge button
        else:
            self.alertLabel.text = 'Alert not Detected '
            self.alertLabel2.text = ''
            self.sw4.active = False
            self.sw4.disabled = True
        

def switch_callback1(switchObject, switchValue): #output status of the switch (visual) to the console
    print ('Opening Gate: ',switchValue)
    
def switch_callback2(switchObject, switchValue): #output status of the switch (visual) to the console
    print ('Closing Gate:',switchValue)

def switch_callback3(switchOBject, switchValue):
    #updates activeMsg to have Ack 1
    alertData2 = {'username': 'ben', 'password':'benpass', 'ACK':1}
    res3 = requests.post("https://testbuild322.000webhostapp.com/scripts/app_alert2.php", json=alertData2)
    print (res3.text)
    
        


def call_popup():
    content = Button(text = 'Warning, gate cannot open and close at the same time!')
    popup = Popup(title = 'Warning', content =content, auto_dismiss=False)
    content.bind(on_press=popup.dismiss)
    popup.open()

class SwitchExample(App):    #build function
    def build(self):
        return SwitchContainer()

if __name__ == '__main__':
    SwitchExample().run()

