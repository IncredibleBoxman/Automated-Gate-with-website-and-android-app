<?php
    require_once __DIR__ . '/../required/db_connect.php';
?>
<?php
    $error=0; $out_json = array(); $out_json['success'] = 1; //assume success
    $label = array();
    $data = array();

    if ($stmt=$mysqli->prepare("SELECT msgID, COUNT(logID) AS num FROM transLogs GROUP BY msgID")) {
        $stmt->execute();
        $stmt->bind_result($msgID,$num);
        while ($stmt->fetch()) {
            $tempL = "MSG " . $msgID;
            array_push($label, $tempL);
            array_push($data, $num);
        }
        $stmt->close();
    }
    else{
        $error=1;
        $mysqli->close();
    }
    if ($error){
        $out_json['success'] = 0; 	//flag failure
    }
    $out_json['labels'] = $label; $out_json['data'] = $data;
    $out_json['error'] = $error;  //provide error (if any) number for debugging
    echo json_encode($out_json);  //encode the data in json format
?>