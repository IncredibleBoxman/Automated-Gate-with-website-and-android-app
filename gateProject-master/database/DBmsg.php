<?php
    require_once __DIR__ . '/../required/db_connect.php';
?>
<?php
    date_default_timezone_set("America/Chicago");
    if ($stmt=$mysqli->prepare("SELECT * FROM msg")) {
        $stmt->execute();
        $stmt->bind_result($msgID,$msgtype,$msgdesc);
        $t=time();
        echo "Time-stamp of last update from DBOR: " . date("m/d/Y h:i:s A",$t);
        printf("<table id='msg'><tr><th>Msg ID</th><th>Type</th><th>Description</th><tr>");
        while ($stmt->fetch()) {
            echo "<tr><td>" . $msgID . "</td><td>" . $msgtype . "</td><td>" . $msgdesc . "</td></tr>";
        }
        $stmt->close();
        printf("</table>");
    }
    else{
        echo "error";
        $mysqli->close();
    }
?>