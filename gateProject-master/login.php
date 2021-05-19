<?php
    require_once __DIR__ . '/required/db_connect.php';
    if (session_status() == PHP_SESSION_NONE) { //check if session has not been started
        session_start();    //if not, start one
    }
    
    extract($_POST);
    if(isset($submit)) {
        if($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")) {
            $stmt->bind_param('s', $username);
            $stmt->execute(); $stmt->store_result(); //store_result to get num_rows etc.
            $stmt->bind_result($db_password); //get the hashed password
            $stmt->fetch();
            if($stmt->num_rows == 1) { //if user exists, verify the password
                if (password_verify($password, $db_password)) {
                    if ($stmt = $mysqli->prepare("INSERT INTO transLogs (msgID, info) VALUES (7,?)")) { //update LED1
                        $stmt->bind_param('s', $username); $stmt->execute();
                        $_SESSION['pname'] = $username;
                    } else {
                        echo '<script>alert("Error: Something went wrong. Try again later.")</script>';
                    }
                }
                else {
                    echo '<script>alert("Incorrect Password")</script>';
                }
                $stmt->close();
            }
            else {
                echo '<script>alert("Incorrect Username")</script>';
            }
        }
        else {
            echo '<script>alert("Error: Unable to retrieve user credentials. Try again later.")</script>';
        }
    }
    if (isset( $_SESSION['pname'])) {
        header('Location: http://ec2-18-224-15-99.us-east-2.compute.amazonaws.com/');
        exit();
    }
?>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="/css/style2.css"/>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <div class="head1">
            <h1>Login to Yessenia Rodriguez’s Main Webpage</h1>
        </div>
        <div class="body1">
			<form id="loginForm" method="post" onSubmit="return loginFnc()">
			    <i class="fa fa-user-circle" aria-hidden="true" style="font-size:150px;color:white;"></i></br>
			    <input type="text" id="username" name="username" placeholder="Username"></br>
			    <input type="password" id="password" name="password" placeholder="Password"></br>
			    <input type="submit" id="submit" name="submit" value="Login">
			</form>
        </div>
        
        <script>
            function loginFnc() {
                let v1 = document.getElementById("username");
                let v2 = document.getElementById("password");
                if(v1.value == "") {
                    alert("Error: Username may not be left empty.");
                    return false;
                    }
                    if(v2.value == "") {
                        alert("Error: Password may not be left empty.");
                        return false;
                    }
            }
        </script>
    </body>
</html>