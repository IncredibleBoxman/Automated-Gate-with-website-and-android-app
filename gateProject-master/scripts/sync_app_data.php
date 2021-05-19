<?php
require_once __DIR__ . '/../required/db_connect.php';
$input = file_get_contents("php://input");
$error=0; $out_json = array(); $out_json['success'] = 1; //assume success
$SW1_status=0; $SW2_status=0; $SW3_status=0; $LED1_status=0; $LED2_status=0;    //updated values
if ($input) {
    $json = json_decode($input, true);	//check if it json input
    if (json_last_error() == JSON_ERROR_NONE) {
        if (isset($json["username"]) && isset($json["password"]) && isset($json["SW1"]) && isset($json["SW2"]) && isset($json["SW3"]) && isset($json["LED1"]) && isset($json["LED2"]) ) {
            $in_username = $json["username"];  
            $in_password = $json["password"];  //if the expected fields are not null, get them
            $in_SW1 = $json["SW1"];
            $in_SW2 = $json["SW2"];
            $in_SW3 = $json["SW3"];
            $in_LED1 = $json["LED1"];
            $in_LED2 = $json["LED2"];
            
            if ($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")) {
                $stmt->bind_param('s', $in_username);
                $stmt->execute();  $stmt->store_result();	//store_result to get num_rows etc.
                $stmt->bind_result($db_password);	//get the hashed password
                $stmt->fetch();
                if ($stmt->num_rows == 1) {		//if user exists, verify the password
                    if (password_verify($in_password, $db_password)) {
                        $stmt->close();
                        if ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'LED1'")) { //read previous LED1 status
                            $stmt->execute(); $stmt->bind_result($LED1_statusP); $stmt->fetch();
                            if($LED1_statusP != $in_LED1){  //if there is a change
                                $stmt->close();
                                if ($stmt = $mysqli->prepare("UPDATE devices set status=? where devID = 'LED1'")) { //update LED1
                                    $stmt->bind_param('i', $in_LED1); $stmt->execute();
                                } else {$error=1;} 
                                $stmt->close();

                                if($in_LED1 == 1) {
                                    if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID) VALUES (11)")) {
                                        $stmt->execute();
                                    } else {$error=2;}
                                    $stmt->close();
                                }
                            }
			            } else {$error=3;} 
                        $stmt->close();
                        
                        if ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'LED2'")) { //read previous LED2 status
                            $stmt->execute(); $stmt->bind_result($LED2_statusP); $stmt->fetch();
                            if($LED2_statusP != $in_LED2){  //if there is a change
                                $stmt->close();
                                if ($stmt = $mysqli->prepare("UPDATE devices set status=? where devID = 'LED2'")) { //update LED2
                                    $stmt->bind_param('i', $in_LED2); $stmt->execute();
                                } else {$error=4;} 
                                $stmt->close();

                                if ($in_LED2 == 1){ //update translog table
                                    if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID) VALUES (12)")) {
                                        $stmt->execute();
                                    } else {$error=5;}
                                    $stmt->close();
                                }
                            }
			            } else {$error=6;} 
                        $stmt->close();
                        
                        //read values from database after changes for LEDs was made
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'SW1'"))) {  //read SW1
                            $stmt->execute(); $stmt->bind_result($SW1_status); $stmt->fetch();
                        } else {$error=7;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'SW2'"))) { //read SW2
                           $stmt->execute(); $stmt->bind_result($SW2_status); $stmt->fetch();
                        } else {$error=8;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'SW3'"))) { //read SW3
                           $stmt->execute(); $stmt->bind_result($SW3_status); $stmt->fetch();
                        } else {$error=9;}
                        $stmt->close();
			            if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'LED1'"))) { //read LED1
                           $stmt->execute(); $stmt->bind_result($LED1_status); $stmt->fetch();
			            } else {$error=10;}
			            $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM devices where devID = 'LED2'"))) { //read LED2
                           $stmt->execute(); $stmt->bind_result($LED2_status); $stmt->fetch();
                        } else {$error=11;}
                        $stmt->close();

                        //in_SW1 is the previous value...LED1_statusP is the previous value -- checking for change
                        if(!$error && ($in_SW1 != $SW1_status || $LED1_statusP != $LED1_status)){
                            if ($LED1_status == 0 && $SW1_status ==1){  //if there is change, check if need to activate message/alarm
                                if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID, info) VALUES (10, 'gate not opening')")){
                                    $stmt->execute();
                                } else {$error=12;}
                            }
                        }

                        //in_SW1 is the previous value...in_SW3 is the previous value -- checking for change
                        if(!$error && ($in_SW1 != $SW1_status || $in_SW3 != $SW3_status)){
                            if ($SW1_status == 1 && $SW3_status ==1){  //if there is change, check if need to activate message/alarm
                                if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID) VALUES (4)")){
                                    $stmt->execute();
                                } else {$error=13;}
                            }
                        }

                        //LED1_statusP is the previous value...LED2_statusP is the previous value -- checking for change
                        //consider if the app caught the error
                        if(!$error && ($inLED1_statusP_SW1 != $LED1_status || $LED2_statusP != $LED2_status)){
                            if ($LED1_status == 1 && $LED2_status ==1){  //if there is change, check if need to activate message/alarm
                                if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID,info) VALUES (10, 'opening and closing gate')")){
                                    $stmt->execute();
                                } else {$error=14;}
                            }
                        }

                        //LED2_statusP is the previous value...in_SW2 is the previous value -- checking for change
                        if(!$error && ($LED2_statusP != $LED2_status || $in_SW2 != $SW2_status)){
                            if ($LED2_status == 1 && $SW2_status ==1){  //if there is change, check if need to activate message/alarm
                                if($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID, info) VALUES (10, 'gate keeps closing')")){
                                    $stmt->execute();
                                } else {$error=15;}
                            }
                        }

                    } else {$error=16;}
                } else {$error=17;}
            } else {$error=18;}
        } else {$error=19;}
    } else {$error=20;}
} else {$error=21;}
if ($error){
   $out_json['success'] = 0; 	//flag failure
}
$out_json['SW1'] = $SW1_status; $out_json['SW2'] = $SW2_status; $out_json['SW3'] = $SW3_status; $out_json['LED1'] = $LED1_status; $out_json['LED2'] = $LED2_status;  
$out_json['error'] = $error;  //provide error (if any) number for debugging
echo json_encode($out_json);  //encode the data in json format
?>