<?php
    require_once __DIR__ . '/../required/db_connect.php';
?>
<?php
    date_default_timezone_set("America/Chicago");
    if ($stmt=$mysqli->prepare("SELECT *, DATE_FORMAT(convert_tz(ts,'+00:00','-05:00'), '%Y-%m-%d %r') FROM transLogs")) {
        $stmt->execute();
        $stmt->bind_result($logID,$timestamp,$msgL,$data,$time12);
        $t=time();
        echo "Time-stamp of last update from DBOR: " . date("m/d/Y h:i:s A",$t);
        printf("<table id='transLogs'><tr><th>Log ID</th><th>Timestamp</th><th>Msg ID</th><th>Data</th><tr>");
        while ($stmt->fetch()) {
            echo "<tr><td>" . $logID . "</td><td>" . $time12 . "</td><td>" . $msgL . "</td><td>" . $data . "</td></tr>";
        }
        $stmt->close();
        printf("</table>");
    }
    else{
        echo "error";
        $mysqli->close();
    }
?>