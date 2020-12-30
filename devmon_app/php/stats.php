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
<a href="current">Devices Data</a>
<a href="stats" class="active">PV Statistics</a>
</div>
<center>
<select id="userSelector">
<option value="df373c7036e73d">RPI</option>
<option value="a0e1f8b095a1ab">PIZ</option>
</select>
<button onclick="getSanternoStatsCharts()">&#x21BB;</button>
</center>
<noscript><b>JavaScript is required for getting the page content. Please enable it in your browser.</b></noscript>
<script type="text/javascript">getSanternoStatsCharts()</script>
<div class="center">
</div>
<div class="center" id="chartsContainer"></div>
</body>
</html>';

function GenerateStatisticsPage(){
    global $page_template;

    return $page_template;
}

echo GenerateStatisticsPage();
?>
