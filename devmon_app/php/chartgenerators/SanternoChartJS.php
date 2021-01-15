<?php
require_once 'utils/ChartJSHelper.php';

class SanternoChartJS
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
            "santernoAcData" => $this->PrepareAcPowerData(),
            "santernoDcData" => $this->PrepareDcPowerData(),
            "santernoTemperatureData" => $this->PrepareInverterTemperatureData()
        );
    }

    private function PrepareAcPowerData()
    {
        $max = 0;
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Power (W)',
            'position' => 'left'
        );

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $head_keys = array_keys(current($this->JsonData));

        $acDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_ac_power") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $acDataSetIdx++);
        }

        $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet('Power Total', 'y', $acDataSetIdx++);

        $total = 0;

        foreach ($this->JsonData as $key => $vals) {
            $chartConfig['data']['labels'][] = $key;
            $acDataSetIdx = 0;
            $acSum = 0;
            foreach ($vals as $key => $val) {
                if (strpos($key, "_ac_power") > 0) {
                    $chartConfig['data']['datasets'][$acDataSetIdx++]['data'][] = $val;
                    $acSum += $val;
                    $total += $val;

                    if ($acSum > $max)
                        $max = $acSum;
                }
            }

            $chartConfig['data']['datasets'][$acDataSetIdx++]['data'][] = $acSum;
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        $count = count($this->JsonData);
        $avg = ($total / $count);
        $prod = (($avg * ($count / 120.0)) / 1000);

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => sprintf("[2x Santerno 3kW] -> AC | Max %d W | Avg %.2f W | Produced %.2f kWh |", $max, $avg, $prod)
        );

        return $chartConfig;
    }

    private function PrepareDcPowerData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y1',
            'name' => 'Voltage (V)',
            'position' => 'left'
        );
        $optCfg[] = array(
            'id' => 'y2',
            'name' => 'Current (A)',
            'position' => 'right',
            'displayLines' => false
        );

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $head_keys = array_keys(current($this->JsonData));

        $dcDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "_dc_voltage") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y1', $dcDataSetIdx++);

            if (strpos($key, "_dc_current") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y2', $dcDataSetIdx++);
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            $dcDataSetIdx = 0;
            $chartConfig['data']['labels'][] = $key;

            foreach ($vals as $key => $val) {
                if (strpos($key, "_dc_voltage") > 0 || strpos($key, "_dc_current") > 0)
                    $chartConfig['data']['datasets'][$dcDataSetIdx++]['data'][] = $val;
            }
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => "[2x Santerno 3kW] -> DC Power data"
        );

        return $chartConfig;
    }

    private function PrepareInverterTemperatureData()
    {
        $chartConfig = array();

        $optCfg[] = array(
            'id' => 'y',
            'name' => 'Temperature (C)',
            'position' => 'left'
        );

        $chartConfig['type'] = 'line';
        $chartConfig['data']['labels'] = array();

        $head_keys = array_keys(current($this->JsonData));

        $tempDataSetIdx = 0;

        foreach ($head_keys as $key) {
            if (strpos($key, "cpu_temperature") > 0 || strpos($key, "radiator_temperature") > 0)
                $chartConfig['data']['datasets'][] = ChartJSHelper::GetDataSet($key, 'y', $tempDataSetIdx++);
        }

        $chartConfig['options'] = ChartJSHelper::GetOptions($optCfg);

        foreach ($this->JsonData as $key => $vals) {
            $tempDataSetIdx = 0;
            $chartConfig['data']['labels'][] = $key;

            foreach ($vals as $key => $val) {
                if (strpos($key, "cpu_temperature") > 0 || strpos($key, "radiator_temperature") > 0)
                    $chartConfig['data']['datasets'][$tempDataSetIdx++]['data'][] = $val;
            }
        }

        $chartConfig['options']['title'] = array(
            'display' => true,
            'text' => "[2x Santerno 3kW] -> Inverters temperature"
        );

        return $chartConfig;
    }
}

?>
