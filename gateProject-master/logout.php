<?php
    require_once __DIR__ . '/required/db_connect.php';
    if (session_status() == PHP_SESSION_NONE) { //check if session has not been started
        session_start();    //if not, start one
    }
   
    if ($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID, info) VALUES (9,?)")) { //update LED1
        $stmt->bind_param('s', $_SESSION['pname']); $stmt->execute();
        if(session_destroy()) {
            header("Location: login.php");
        }
    } else {
        echo '<script>alert("Error: Something went wrong. Try again later.")</script>';
    }
?>