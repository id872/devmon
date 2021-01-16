<?php
require_once 'sql/PurifierJsonDataGetter.php';
require_once 'sql/SanternoJsonDataGetter.php';
require_once 'sql/DS18B20JsonDataGetter.php';
require_once 'sql/TasmotaJsonDataGetter.php';
require_once 'chartgenerators/PurifierChartJS.php';
require_once 'chartgenerators/TasmotaChartJS.php';
require_once 'chartgenerators/DS18B20ChartJS.php';
require_once 'chartgenerators/SanternoChartJS.php';
require_once 'chartgenerators/SanternoStatsChartJS.php';

function getChartData($dataType, $dateFrom, $dateTo, $userHash)
{
    $jsonData = NULL;
    $chartGenerator = NULL;
    $dateDiff = date_diff(date_create($dateFrom), date_create($dateTo))->format('%r%a');

    if ($dateDiff > 30 || $dateDiff < 0)
        return NULL;

    if ($dataType === 'santerno_readouts') {
        $jsonData = (new SanternoJsonDataGetter($userHash))->getData($dateFrom);
        $chartGenerator = new SanternoChartJS($jsonData);
    } else if ($dataType === 'ds18b20_readouts') {
        $jsonData = (new DS18B20JsonDataGetter($userHash))->getData($dateFrom, $dateTo);
        $chartGenerator = new DS18B20ChartJS($jsonData);
    } else if ($dataType === 'purifier_readouts') {
        $jsonData = (new PurifierJsonDataGetter($userHash))->getData($dateFrom, $dateTo);
        $chartGenerator = new PurifierChartJS($jsonData);
    } else if ($dataType === 'tasmota_readouts') {
        $jsonData = (new TasmotaJsonDataGetter($userHash))->getData($dateFrom, $dateTo);
        $chartGenerator = new TasmotaChartJS($jsonData);
    } else
        return json_encode(array(), JSON_NUMERIC_CHECK);

    if ($chartGenerator != NULL)
        return json_encode($chartGenerator->PrepareChart(), JSON_NUMERIC_CHECK);

    return json_encode(array(), JSON_NUMERIC_CHECK);
}

function getSanternoStatsChartData($userHash)
{
    $jsonData = (new SanternoJsonDataGetter($userHash))->getMonthStatsData();
    return json_encode((new SanternoStatsChartJS($jsonData))->PrepareChart(), JSON_NUMERIC_CHECK);
}

if (isset($_POST["type"]) && isset($_POST["dateFrom"]) && isset($_POST["dateTo"]) && isset($_POST["hash"]))
    echo getChartData($_POST["type"], $_POST["dateFrom"], $_POST["dateTo"], $_POST["hash"]);

if (isset($_POST["stats"]) && isset($_POST["hash"]))
    echo getSanternoStatsChartData($_POST["hash"]);

?>
