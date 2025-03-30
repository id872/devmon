<?php

require_once 'utils/ChartJSHelper.php';

class AhtEnsChartJS
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
            "ahtEnsParticlesData" => $this->PrepareAhtEnsParticlesData(),
            "ahtEnsAqiData" => $this->PrepareAhtEnsAqiData(),
            "ahtEnsTempHumData" => $this->PrepareAhtEnsTempHumData()
        );
    }

    private function PrepareAhtEnsParticlesData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'CO2 parts per million (ppm)',
            'position' => 'left',
            'displayLines' => true
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'TVOC parts per billion (ppb)',
            'position' => 'right',
            'displayLines' => false
        );

        $head_keys = array_keys(end($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_eco2") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $tDataSetIdx++);
            }

            if (strpos($key, "_tvoc") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);
        $chartConfig['options']['scales']['y1']['min'] = 400;
        $chartConfig['options']['scales']['y2']['min'] = 0;

        foreach ($this->JsonData as $key => $vals) {
            $chartConfig['data']['labels'][] = $key;

            $tDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_eco2") > 0 || strpos($key, "_tvoc") > 0) {
                    $chartConfig['data']['datasets'][$tDataSetIdx++]['data'][] = $val;
                }
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "ENS160 particles chart"
        );

        return $chartConfig;
    }

    private function PrepareAhtEnsAqiData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'AQI',
            'position' => 'left',
            'displayLines' => true
        );

        $head_keys = array_keys(end($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_aqi") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);
        $chartConfig['options']['scales']['y']['min'] = 1;
        $chartConfig['options']['scales']['y']['max'] = 5;
        $chartConfig['options']['scales']['y']['ticks']['stepSize'] = 1;

        $chartConfig['aqiLabels']['1'] = 'Excellent';
        $chartConfig['aqiLabels']['2'] = 'Good';
        $chartConfig['aqiLabels']['3'] = 'Moderate';
        $chartConfig['aqiLabels']['4'] = 'Poor';
        $chartConfig['aqiLabels']['5'] = 'Unhealthy';

        foreach ($this->JsonData as $key => $vals) {
            $chartConfig['data']['labels'][] = $key;

            $tDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_aqi") > 0) {
                    $chartConfig['data']['datasets'][$tDataSetIdx++]['data'][] = $val;
                }
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "ENS160 AQI-UBA air quality index chart"
        );

        return $chartConfig;
    }

    private function PrepareAhtEnsTempHumData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'Temperature (C)',
            'position' => 'left',
            'displayLines' => true
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'Humidity (%)',
            'position' => 'right',
            'displayLines' => false
        );

        $head_keys = array_keys(end($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $tDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_temperature") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $tDataSetIdx++);
            }

            if (strpos($key, "_humidity") > 0) {
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $tDataSetIdx++);
            }
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            $chartConfig['data']['labels'][] = $key;

            $tDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_temperature") > 0 || strpos($key, "_humidity") > 0) {
                    $chartConfig['data']['datasets'][$tDataSetIdx++]['data'][] = $val;
                }
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "AHT21 Temperature/Humidity chart"
        );

        return $chartConfig;
    }
}
