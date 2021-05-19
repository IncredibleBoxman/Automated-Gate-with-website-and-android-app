<?php
    require_once __DIR__ . '/required/db_connect.php';
    if (session_status() == PHP_SESSION_NONE) { //check if session has not been started
        session_start();    //if not, start one
    }
    if (!isset( $_SESSION['pname'])) {
        header('Location: login.php');
        exit();
    }
?>
<html>
    <head>
        <title>Welcome</title>
        <link rel="stylesheet" type="text/css" href="/css/style1.css"/>
        <script type="text/javascript" src="/js/jquery.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
        <!-- Import D3 Scale Chromatic via CDN -->
        <script src="https://d3js.org/d3-color.v1.min.js"></script>
        <script src="https://d3js.org/d3-interpolate.v1.min.js"></script>
        <script src="https://d3js.org/d3-scale-chromatic.v1.min.js"></script>
        <!-- Import chartjs-plugin-labels via CDN obtained from https://github.com/emn178/chartjs-plugin-labels -->
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
    </head>
    <body>
        <div class="head1">
            <?php
                echo "<h1>Welcome " . $_SESSION['pname'] . " to Yessenia Rodriguez's Main Webpage</h1>";
            ?>
            <button id="logout" onclick='document.location.href="logout.php"'>Logout</button>
        </div>
        <div class="body1">
			<div class="tableHeader" id="deviceH">
				<button onclick='show("Devices");'>Device</button>
			</div>
			<div id="tableDevices"></div>
			</br>
			<div class="tableHeader" id="msgH">
				<button onclick='show("Msg");'>Messages</button>
			</div>
			<div id="tableMsg"></div>
			</br>
			<div class="tableHeader" id="activeH">
				<button onclick='show("Active");'>Active Messages</button>
			</div>
			<div id="tableActive"></div>
			</br>
			<div class="tableHeader" id="logH">
				<button onclick='show("Logs");'>Transactional Logs</button>
			</div>
			<div id="tableLogs"></div>
			</br>
			<div class="tableHeader" id="chartH">
				<button onclick='show("Charts");'>Charts</button>
			</div>
			<div id="tableCharts">
                </br>
                <div class="tableHeader" id="hourH">
				    <button onclick='show2("Hour");'>Transactions per Hour</button>
			    </div>
                <div id="chartHour"></div>
                </br>
                <div class="tableHeader" id="msgFH">
				    <button onclick='show2("Msg");'>Message Frequency</button>
			    </div>
                <div id="chartMsg"></div>
                </br>
                <div class="tableHeader" id="trafficH">
				    <button onclick='show2("Traffic");'>Gate Traffic</button>
			    </div>
                <div id="chartTraffic"></div>
                </br>
            </div>
			</br>
			
            <script type="text/javascript">
                $(document).ready(function() {
                    //load the first time without dealy
                    $('#tableDevices').load('/database/DBdevices.php');
					$('#tableMsg').load('/database/DBmsg.php');
					$('#tableActive').load('/database/DBactive.php');
                    $('#tableLogs').load('/database/DBlogs.php');
                    $('#chartHour').load('/chart/hourChart.php');
                    $('#chartMsg').load('/chart/msgChart.php');
                    $('#chartTraffic').load('/chart/trafficChart.php');

                    setInterval(function() {
                        $('#tableDevices').load('/database/DBdevices.php');
						$('#tableMsg').load('/database/DBmsg.php');
						$('#tableActive').load('/database/DBactive.php');
                        $('#tableLogs').load('/database/DBlogs.php');
                        $('#chartHour').load('/chart/hourChart.php');
                        $('#chartMsg').load('/chart/msgChart.php');
                        $('#chartTraffic').load('/chart/trafficChart.php');
                    }, 30000);
                });
                
                function show(nr) {
                    //check if showing before resetting
                    var active = document.getElementById("table"+nr);
                    var flag = (window.getComputedStyle(active).display == "none");
                    
                    //reset all tables to not display...allow only one to show at a time
                    document.getElementById("tableDevices").style.display="none";
                    document.getElementById("tableMsg").style.display="none";
                    document.getElementById("tableActive").style.display="none";
                    document.getElementById("tableLogs").style.display="none";
                    document.getElementById("tableCharts").style.display="none";
                    
                    //if it was not displaying before...make it display
                    if(flag){
                        document.getElementById("table"+nr).style.display="block";
                    }
                }

                function show2(nr) {
                    //check if showing before resetting
                    var active = document.getElementById("chart"+nr);
                    var flag = (window.getComputedStyle(active).display == "none");
                    
                    //reset all tables to not display...allow only one to show at a time
                    document.getElementById("chartHour").style.display="none";
                    document.getElementById("chartMsg").style.display="none";
                    document.getElementById("chartTraffic").style.display="none";
                    
                    //if it was not displaying before...make it display
                    if(flag){
                        document.getElementById("chart"+nr).style.display="block";
                    }
                }
            </script>
        </div>
    </body>
</html>