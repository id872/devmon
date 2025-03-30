<?php

require_once 'sql/UsersGetter.php';

$page_template = '<!DOCTYPE html>
<html>
<head>
<title>Monitoring</title>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<script src="../js/chart.umd.js"></script>
<script src="../js/jquery-3.7.1.min.js"></script>
<script src="../js/charts.js"></script>
</head>
<body>
<div class="navBar">
<a href="current">Devices Data</a>
<a href="stats" class="active">PV Statistics</a>
</div>
<center>
<select id="userSelector">
%1$s
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

function GetUsers()
{
    $data = (new UsersGetter(null))->getData();
    $strPattern = '<option value="%1$s">%2$s</option>';
    $userOptions = array();

    foreach ($data as $row) {
        $userOptions[] = sprintf($strPattern, $row['api_hash'], $row['user_name']);
    }

    return join('', $userOptions);
}

function GenerateStatisticsPage()
{
    global $page_template;

    return sprintf($page_template, GetUsers());
}

echo GenerateStatisticsPage();
