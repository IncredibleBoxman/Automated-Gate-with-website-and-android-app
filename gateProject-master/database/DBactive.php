<?php
    require_once __DIR__ . '/../required/db_connect.php';
?>
<?php
    date_default_timezone_set("America/Chicago");
    if ($stmt=$mysqli->prepare("SELECT * FROM activeMsg")) {
        $stmt->execute();
        $stmt->bind_result($msgID,$since,$ack);
        $t=time();
        echo "Time-stamp of last update from DBOR: " . date("m/d/Y h:i:s A",$t);
        printf("<table id='active_msg'><tr><th>Msg ID</th><th>Active Since</th><th>ACK</th><tr>");
        while ($stmt->fetch()) {
            echo "<tr><td>" . $msgID . "</td><td>" . $since . "</td><td>" . $ack . "</td></tr>";
        }
        $stmt->close();
        printf("</table>");
    }
    else{
        echo "error";
        $mysqli->close();
    }
?>