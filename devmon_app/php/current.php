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
<option value="0">Santerno PV Data</option>
<option value="1">DS18B20 Data</option>
<option value="2">Xiaomi AirPurifier</option>
<option value="3">Tasmota Plug</option>
</select>
<select id="userSelector">
<option value="df373c7036e73d">RPI</option>
<option value="a0e1f8b095a1ab">PIZ</option>
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
