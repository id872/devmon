<?php
require_once 'utils/ChartJSHelper.php';

class DS18B20ChartJS
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
            "da18b20TemperatureData" => $this->PrepareDs18b20SensorTemperatures()
        );
    }

    private function PrepareDs18b20SensorTemperatures()
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

        $tDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_temperature") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $tDataSetIdx++);
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            array_push($chartConfig['data']['labels'], $key);
            $tDataSetIdx = 0;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_temperature") > 0)
                    array_push($chartConfig['data']['datasets'][$tDataSetIdx++]['data'], $val);
            }
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => "[DS18B20 sensor] -> Temperature data"
        );

        return $chartConfig;
    }
}

?>
