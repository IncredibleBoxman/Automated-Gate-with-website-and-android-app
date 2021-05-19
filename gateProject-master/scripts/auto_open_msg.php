<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");
$error=0; $out_json = array(); $out_json['success'] = 1; //assume success
$SW1_status=0; $SW2_status=0; $SW3_status=0; $LED1_status=0; $LED2_status=0;
if ($input) {
    $json = json_decode($input, true);	//check if it json input
    if (json_last_error() == JSON_ERROR_NONE) {
        if (isset($json["username"]) && isset($json["password"])) {
            $in_username = $json["username"];  
            $in_password = $json["password"];  //if the expected fields are not null, get them
            if ($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")) {
                $stmt->bind_param('s', $in_username);
                $stmt->execute();  $stmt->store_result();	//store_result to get num_rows etc.
                $stmt->bind_result($db_password);	//get the hashed password
                $stmt->fetch();
                if ($stmt->num_rows == 1) {		//if user exists, verify the password
                    if (password_verify($in_password, $db_password)) {
			  $stmt->close();
                        $stmt = $mysqli->prepare("INSERT INTO transLogs (msgID, info) VALUES (11, 'auto')");
        $stmt->execute();
                    } else {$error=1;}
                } else {$error = 2;}
            }else {$error = 3;}
        } else {$error = 4;}
    } else {$error = 5;}
} else {$error = 6;}
if ($error){
   $out_json['success'] = 0; 	//flag failure
}

?>