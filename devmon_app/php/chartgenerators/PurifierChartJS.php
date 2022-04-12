<?php
require_once 'utils/ChartJSHelper.php';

class PurifierChartJS
{

    private $JsonData = NULL;

    function __construct(&$jsonData)
    {
        $this->JsonData = &$jsonData;
    }

    public function PrepareChart()
    {
        if (! $this->JsonData)
            return array();

        return array(
            "purifierPurityData" => $this->PreparePurifierPurityData(),
            "purifierTemperatureData" => $this->PreparePurifierTemperatureData()
        );
    }

    private function PreparePurifierPurityData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'Purity (AQI)',
            'position' => 'left'
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'FAN Speed (RPM)',
            'position' => 'right',
            'displayLines' => true
        );

        $head_keys = array_keys(current($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $afDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_aqi") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $afDataSetIdx++);
            else if (strpos($key, "_fan_rpm") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $afDataSetIdx++);
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            array_push($chartConfig['data']['labels'], $key);

            $afDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_aqi") > 0 || strpos($key, "_fan_rpm") > 0)
                    array_push($chartConfig['data']['datasets'][$afDataSetIdx++]['data'], $val);
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "Purifier Purity/FAN_RPM chart"
        );

        return $chartConfig;
    }

    private function PreparePurifierTemperatureData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'Humidity (%)',
            'position' => 'left'
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'Temperature (C)',
            'position' => 'right',
            'displayLines' => true
        );

        $head_keys = array_keys(current($this->JsonData));

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $thDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_humidity") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $thDataSetIdx++);
            else if (strpos($key, "_temperature") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $thDataSetIdx++);
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            array_push($chartConfig['data']['labels'], $key);

            $thDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_humidity") > 0 || strpos($key, "_temperature") > 0)
                    array_push($chartConfig['data']['datasets'][$thDataSetIdx++]['data'], $val);
            }
        }

        $chartConfig['options']['plugins']['title'] = array(
            'display' => true,
            'text' => "Purifier Humidity/Temperature chart"
        );

        return $chartConfig;
    }
}

?>
