<?php

require_once('SqlRequest.php');
require_once('SqlQuery.php');

class DeviceDataSaver extends SqlRequest 
{
    const MAX_OMMIT_INSERT_COUNT = 1;
    const JSON_DATA_TYPE_IDS = array(
        'power_readouts' => 0,
        'temperature_readouts' => 1,
        'purifier_readouts' => 2,
        'tasmota_readouts' => 3
    );

    private function insertReadoutTime($dev_data_type, $readout)
    {
        $queryOK = FALSE;
        $userID = $this->getUserData("user_id");

        $query = "INSERT INTO data_logs (user_id, dev_data_type, readout_time) 
            VALUES ({$userID}, {$dev_data_type}, ?)";

        if ($dev_data_type === 0 &&
            date('G:i', strtotime($readout["readout_time"])) === '14:00')
            SqlQuery::updatePowerDayStats($this->Connection);

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "s", $readout["readout_time"]);
            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function insertPowerData($readout)
    {
        $queryOK = FALSE;
        $deviceID = $this->getDeviceId($readout["dev_name"]);

        $query = "INSERT INTO power_data_readings 
            (data_id, device_id, ac_power, dc_voltage, dc_current, cpu_temperature, radiator_temperature) 
            VALUES (LAST_INSERT_ID(), {$deviceID}, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "ddddd",
                $readout["ac_power"], $readout["dc_voltage"],
                $readout["dc_current"], $readout["cpu_temperature"],
                $readout["radiator_temperature"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function insertTemperatureData($readout)
    {
        $queryOK = FALSE;
        $deviceID = $this->getDeviceId($readout["dev_name"]);

        $query = "INSERT INTO temperature_data_readings
            (data_id, device_id, temperature)
            VALUES (LAST_INSERT_ID(), {$deviceID}, ?)";

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "d", $readout["temperature"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function getDeviceId($devName)
    {
        foreach ($this->UserData as $data)
        {
            if ($data["dev_name"] === $devName)
            {
                return $data["device_id"];
            }
        }
        return NULL;
    }

    private function insertPurifierData($readout)
    {
        $queryOK = FALSE;
        $deviceID = $this->getDeviceId($readout["dev_name"]);

        $query = "INSERT INTO purifier_data_readings
            (data_id, device_id, aqi, humidity, temperature, fan_rpm)
            VALUES (LAST_INSERT_ID(), {$deviceID}, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "dddd",
                $readout["aqi"], $readout["humidity"],
                $readout["temperature"], $readout["fan_rpm"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function insertTasmotaData($readout)
    {
        $queryOK = FALSE;
        $deviceID = $this->getDeviceId($readout["dev_name"]);

        $query = "INSERT INTO tasmota_data_readings
            (data_id, device_id, ac_power, ac_voltage, ac_current)
            VALUES (LAST_INSERT_ID(), {$deviceID}, ?, ?, ?)";

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "ddd", 
                $readout["ac_power"], $readout["ac_voltage"],
                $readout["ac_current"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function insertReadoutsToDB($dataType, $readouts)
    {
        $ommitCounter = 0;

        if ($readouts === NULL || count($readouts) < 1)
            return FALSE;

        foreach ($readouts as $readout)
        {
            $queryOK = FALSE;

            if ($ommitCounter > self::MAX_OMMIT_INSERT_COUNT)
            {
                return FALSE;
            }

            if (array_key_exists('readout_time', $readout) || $ommitCounter > 0)
            {
                if (!array_key_exists('readout_time', $readout))
                    continue;
                $queryOK = $this->insertReadoutTime(self::JSON_DATA_TYPE_IDS[$dataType], $readout);
                if ($queryOK === FALSE)
                {
                    $ommitCounter += 1;
                    continue;
                }
                else{
                    $ommitCounter = 0;
                }
            }
            else
            {
                if ($dataType === 'power_readouts')
                {
                    $queryOK = $this->insertPowerData($readout);
                }
                else if ($dataType === 'temperature_readouts')
                {
                    $queryOK = $this->insertTemperatureData($readout);
                }
                else if ($dataType === 'purifier_readouts')
                {
                    $queryOK = $this->insertPurifierData($readout);
                }
                else if ($dataType === 'tasmota_readouts')
                {
                    $queryOK = $this->insertTasmotaData($readout);
                }
                else
                {
                    return FALSE;
                }
            }

            if ($queryOK === FALSE)
            {
                return FALSE;
            }
        }
        return $queryOK;
    }

    public function getAuth()
    {
        $auth["user_password_hash"] = $this->getUserData("user_password_hash");
        $auth["api_key"] = $this->getUserData("api_key");
        return $auth;
    }

    public function addData($json)
    {
        $keys = array_keys($json);
        mysqli_autocommit($this->Connection, FALSE);

        foreach ($keys as $jsonDataType)
        {
            if (strpos($jsonDataType, "_readouts") > 0)
            {
                if ($this->insertReadoutsToDB($jsonDataType, $json[$jsonDataType]))
                {
                    mysqli_commit($this->Connection);
                    echo "_".$jsonDataType."_inserted_ok_";
                }
                else
                {
                    mysqli_rollback($this->Connection);
                }
            }
        }
    }
}

?>
