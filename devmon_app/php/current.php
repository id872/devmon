<?php

require_once 'sql/DataTypesGetter.php';
require_once 'sql/UsersGetter.php';

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
<input type="date" id="dateFrom" value="%1$s" max="%1$s" onchange="getChartsByDate(this)"/>
<input type="date" id="dateTo" value="%1$s" max="%1$s" onchange="getChartsByDate(this)"/>
<select id="dataTypeSelector" onchange="getChartsByDate()">
%2$s
</select>
<select id="userSelector" onchange="getChartsByDate()">
%3$s
</select>
<button onclick="getChartsByDate()">&#x21BB;</button>
</center>
<noscript><b>JavaScript is required for getting the page content. Please enable it in your browser.</b></noscript>
<script type="text/javascript">getChartsByDate();</script>
<div class="center" id="chartsContainer"></div>
</body>
</html>';

function GetDataTypes()
{
    $data = (new DataTypesGetter(NULL))->getData();

    if ($data === FALSE)
        return '';

    $strPattern = '<option value="%1$s">%2$s</option>';
    $options = array();

    foreach($data as $row)
        $options[] = sprintf($strPattern, $row['dt_name'], $row['dt_description']);

    return join('', $options);
}

function GetUsers()
{
    $data = (new UsersGetter(NULL))->getData();

    if ($data === FALSE)
        return '';

    $strPattern = '<option value="%1$s">%2$s</option>';
    $options = array();

    foreach($data as $row)
        $options[] = sprintf($strPattern, $row['api_hash'], $row['user_name']);

    return join('', $options);
}

function GenerateCurrentPage()
{
    global $page_template;
    $today = date("Y-m-d");

    return sprintf($page_template, $today, GetDataTypes() ,GetUsers());
}

echo GenerateCurrentPage();
?>
