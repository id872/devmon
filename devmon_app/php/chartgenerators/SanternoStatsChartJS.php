<?php

require_once('utils/ChartJSHelper.php');

class SanternoStatsChartJS
{
    private $JsonData = NULL;

    function __construct(&$jsonData)
    {
        $this->JsonData =& $jsonData;
    }

    public function PrepareChart()
    {
        $charts = array();

        foreach ($this->JsonData as $key => $vals)
        {
            $charts[sprintf("santernoMonthStatsData_%s", $key)] = $this->PrepareSanternoMonthYearStatsData($key, $vals);
        }

        return $charts;
    }

    private function PrepareSanternoMonthYearStatsData($year, $data)
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Power produced (kWh)',
            'position' => 'left'
        );

        $chartConfig['type'] = 'bar';
        $chartConfig['data']['labels'] = array();

        $head_keys = array_keys(current($data));

        $dataSetIdx = 0;

        foreach($head_keys as $key)
        {
            if (strpos($key, "_kwh") > 0)
            {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $dataSetIdx++);
            }
        }

        $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet('Power Total', 'y', $dataSetIdx++);
        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        $yearTotal = 0;
        foreach ($data as $key => $vals)
        {
            $chartConfig['data']['labels'][] = $key;

            $dataSetIdx = 0;
            $powerTotal = 0;

            foreach ($vals as $key => $val)
            {
                if (strpos($key, "_kwh") > 0)
                {
                    array_push($chartConfig['data']['datasets'][$dataSetIdx++]['data'], $val);
                    $powerTotal += $val;
                }
            }

            array_push($chartConfig['data']['datasets'][$dataSetIdx++]['data'], round($powerTotal, 2));
            $yearTotal += $powerTotal;
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => sprintf("Year %s Power production - Total: %.2f kWh", $year, $yearTotal)
        );

        return $chartConfig;
    }
}

?>
