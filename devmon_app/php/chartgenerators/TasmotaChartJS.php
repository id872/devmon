<?php

require_once('utils/ChartJSHelper.php');

class TasmotaChartJS
{
    private $JsonData = NULL;

    function __construct(&$jsonData)
    {
        $this->JsonData =& $jsonData;
    }

    public function PrepareChart()
    {
        return array(
            "tasmotaPowerData" => $this->PrepareTasmotaPowerData(),
            "tasmotaVolgateCurrentData" => $this->PrepareTasmotaCurrentVoltageData()
        );
    }

    private function PrepareTasmotaPowerData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Power (W)',
            'position' => 'left'
        );

        $head_keys = array_keys(end($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach($head_keys as $key)
        {
            if (strpos($key, "_ac_power") > 0)
            {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals)
        {
            $chartConfig['data']['labels'][] = $key;

            $tDataSetIdx = 0;

            foreach ($vals as $key => $val)
            {
                if (strpos($key, "_ac_power") > 0)
                {
                    $chartConfig['data']['datasets'][$tDataSetIdx++]['data'][] = $val;
                }
            }
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => "Tasmota Power chart"
        );

        return $chartConfig;
    }

    private function PrepareTasmotaCurrentVoltageData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'Voltage (V)',
            'position' => 'left'
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'Curren (A)',
            'position' => 'right'
        );

        $head_keys = array_keys(end($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach($head_keys as $key)
        {
            if (strpos($key, "_ac_voltage") > 0)
            {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $tDataSetIdx++);
            }
            if (strpos($key, "_ac_current") > 0)
            {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals)
        {
            $chartConfig['data']['labels'][] = $key;

            $tDataSetIdx = 0;

            foreach ($vals as $key => $val)
            {
                if (strpos($key, "_ac_voltage") > 0 ||
                    strpos($key, "_ac_current") > 0)
                {
                    $chartConfig['data']['datasets'][$tDataSetIdx++]['data'][] = $val;
                }
            }
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => "Tasmota Voltage/Current chart"
        );

        return $chartConfig;
    }
}

?>
