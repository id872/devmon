<?php

require_once 'utils/ChartJSHelper.php';

class DS18B20ChartJS
{
    private $JsonData = null;

    function __construct(&$jsonData)
    {
        $this->JsonData = &$jsonData;
    }

    public function PrepareChart()
    {
        if (! $this->JsonData) {
            return array();
        }

        return array(
            "da18b20TemperatureData" => $this->PrepareDs18b20SensorTemperatures(),
            //"viessmannDiffTemperatureData" => $this->PrepareViessmannDiffTemp()
        );
    }

    private function PrepareDs18b20SensorTemperatures()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Temperature (C)',
            'position' => 'left',
            'displayLines' => true
        );

        $head_keys = array_keys(current($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach ($head_keys as $sensorName) {
            if (strpos($sensorName, "_temperature") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($sensorName, 'y', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $dateTime => $vals) {
            array_push($chartConfig['data']['labels'], $dateTime);
            $tDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_temperature") > 0) {
                    array_push($chartConfig['data']['datasets'][$tDataSetIdx++]['data'], $val);
                }
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "[DS18B20 sensor] -> Temperature data"
        );

        return $chartConfig;
    }

    private function PrepareViessmannDiffTemp()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Temperature (C)',
            'position' => 'left'
        );

        $head_keys = array_keys(current($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet('Różnica temperatur', 'y', 0);
        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            array_push($chartConfig['data']['labels'], $key);
            $diff = null;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_INLET") > 0 || strpos($key, "_OUTLET")) {
                    if ($diff === null) {
                        $diff = $val;
                    } else {
                        $diff -= $val;
                    }
                }
            }
            array_push($chartConfig['data']['datasets'][0]['data'], round(abs($diff), 2));
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "[Viessmann] -> Różnica temperatur"
        );

        return $chartConfig;
    }
}
