<?php
$page_template = '<!DOCTYPE html>
<html>
<head>
<title>Monitoring</title>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<script src="../js/jquery-3.5.1.min.js"></script>
<script src="../js/Chart.js"></script>
<script src="../js/charts.js"></script>
</head>
<body>
<div class="navBar">
<a href="current" class="active">Devices Data</a>
<a href="stats">PV Statistics</a>
</div>
<center>
<input type="date" id="dateFrom" value="%1$s" onchange="getChartsByDate()"/>
<input type="date" id="dateTo" value="%1$s" onchange="getChartsByDate()"/>
<select id="dataTypeSelector" onchange="getChartsByDate()">
<option value="santerno_readouts">Santerno PV Data</option>
<option value="ds18b20_readouts">DS18B20 Data</option>
<option value="purifier_readouts">Xiaomi AirPurifier</option>
<option value="tasmota_readouts">Tasmota Plug</option>
</select>
<select id="userSelector">
<option value="831fe1b07d15ff">PI_Zero</option>
</select>
<button onclick="getChartsByDate()">&#x21BB;</button>
</center>
<noscript><b>JavaScript is required for getting the page content. Please enable it in your browser.</b></noscript>
<script type="text/javascript">getChartsByDate();</script>
<div class="center" id="chartsContainer"></div>
</body>
</html>';

function GenerateCurrentPage()
{
    global $page_template;
    $today = date("Y-m-d");

    return sprintf($page_template, $today);
}

echo GenerateCurrentPage();
?>
