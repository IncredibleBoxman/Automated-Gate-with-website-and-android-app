<?php
    require_once __DIR__ . '/../required/db_connect.php';
?>
<?php
    date_default_timezone_set("America/Chicago");
    if ($stmt=$mysqli->prepare("SELECT * FROM devices")) {
        $stmt->execute();
        $stmt->bind_result($devID,$devtype,$func,$ctrl,$status);
        $t=time();
        echo "Time-stamp of last update from DBOR: " . date("m/d/Y h:i:s A",$t);
        printf("<table id='device'><tr><th>Device Name</th><th>Type</th><th>Function</th><th>Control</th><th>Status</th><tr>");
        while ($stmt->fetch()) {
            echo "<tr><td>" . $devID . "</td><td>" . $devtype . "</td><td>" . $func . "</td><td>" . $ctrl . "</td><td>" . $status . "</td></tr>";
        }
        $stmt->close();
        printf("</table>");
    }
    else{
        echo "error";
        $mysqli->close();
    }
?>