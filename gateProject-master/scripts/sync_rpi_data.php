<?php
require_once __DIR__ . '/../required/db_connect.php';
$input = file_get_contents("php://input");
$error=0; $out_json = array(); $out_json['success'] = 1; //assume success
$SW1_status=0; $SW2_status=0; $SW3_status=0; $LED1_status=0; $LED2_status=0;
if ($input) {
    $json = json_decode($input, true); //check if it json input
    if (json_last_error() == JSON_ERROR_NONE) {
        if (isset($json["username"]) && isset($json["password"]) && isset($json["SW1"]) && isset($json["SW2"]) && isset($json["SW3"]) && isset($json["LED1"]) && isset($json["LED2"])) {
            $in_username = $json["username"];
            $in_password = $json["password"]; //if the expected fields are not null, get them
            $in_SW1 = $json["SW1"];
            $in_SW2 = $json["SW2"];
            $in_SW3 = $json["SW3"];
            $in_LED1 = $json["LED1"];
            $in_LED2 = $json["LED2"];
            if ($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")) {
                $stmt->bind_param('s', $in_username);
                $stmt->execute(); $stmt->store_result(); //store_result to get num_rows etc.
                $stmt->bind_result($db_password); //get the hashed password
                $stmt->fetch();
                if ($stmt->num_rows == 1) { //if user exists, verify the password
                    if (password_verify($in_password, $db_password)) {
                        $stmt->close();
                        if ($stmt = $mysqli->prepare("UPDATE device set status=? where devname = 'SW1'")) { //update SW1
                            $stmt->bind_param('i', $in_SW1); $stmt->execute();
                        } else {$error=1;}
                        $stmt->close();
                        if ($stmt = $mysqli->prepare("UPDATE device set status=? where devname = 'SW2'")) { //update SW2
                            $stmt->bind_param('i', $in_SW2); $stmt->execute();
                        } else {$error=2;}
                        $stmt->close();
                        if ($stmt = $mysqli->prepare("UPDATE device set status=? where devname = 'SW3'")) { //update SW3
                            $stmt->bind_param('i', $in_SW3); $stmt->execute();
                        } else {$error=3;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device where devname = 'SW1'"))) { //read SW1
                            $stmt->execute(); $stmt->bind_result($SW1_status); $stmt->fetch();
                        } else {$error=4;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device where devname = 'SW2'"))) { //read SW2
                            $stmt->execute(); $stmt->bind_result($SW2_status); $stmt->fetch();
                        } else {$error=5;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device where devname = 'SW3'"))) { //read SW3
                            $stmt->execute(); $stmt->bind_result($SW3_status); $stmt->fetch();
                        } else {$error=6;}
                        $stmt->close();
                        if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device where devname = 'LED1'"))) { //read LED1
                            $stmt->execute(); $stmt->bind_result($LED1_status); $stmt->fetch();
                        } else {$error=7;}
                        $stmt->close();
                    } else {$error=8;}
                } else {$error=9;}
            } else {$error=10;}
        } else {$error=11;}
    } else {$error=12;}
} else {$error=13;}
if ($error){
$out_json['success'] = 0; //flag failure
}
$out_json['SW1'] = $SW1_status; $out_json['SW2'] = $SW2_status; $out_json['SW3'] = $SW3_status; $out_json['LED1'] = $LED1_status; $out_json['LED2'] = $LED2_status;
$out_json['error'] = $error; //provide error (if any) number for debugging
echo json_encode($out_json); //encode the data in json format
?>