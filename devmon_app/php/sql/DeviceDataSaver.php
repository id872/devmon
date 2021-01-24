<?php
require_once 'SqlRequest.php';
require_once 'SqlQuery.php';

class DeviceDataSaver extends SqlRequest
{

    const MAX_OMMIT_INSERT_COUNT = 1;

    private function insertReadoutTime($dataTypeName, $readout)
    {
        $queryOK = FALSE;
        $userID = $this->getUserData("user_id");

        $query = "INSERT INTO data_logs (user_id, dt_id, readout_time) 
            VALUES ({$userID}, (SELECT dt_id FROM dev_data_type WHERE dt_name = ?), ?)";

        if ($dataTypeName === 'santerno_readouts' && date('G:i', strtotime($readout["readout_time"])) === '14:00')
            SqlQuery::updatePowerDayStats($this->Connection);

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $dataTypeName, $readout["readout_time"]);
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

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "ddddd", $readout["ac_power"], $readout["dc_voltage"], $readout["dc_current"], $readout["cpu_temperature"], $readout["radiator_temperature"]);

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

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "d", $readout["temperature"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function getDeviceId($devName)
    {
        foreach ($this->UserData as $data) {
            if ($data["dev_name"] === $devName)
                return $data["device_id"];
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

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "dddd", $readout["aqi"], $readout["humidity"], $readout["temperature"], $readout["fan_rpm"]);

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

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "ddd", $readout["ac_power"], $readout["ac_voltage"], $readout["ac_current"]);

            $queryOK = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        return $queryOK;
    }

    private function insertReadoutsToDB($dataTypeName, $readouts)
    {
        $ommitCounter = 0;

        if ($readouts === NULL || count($readouts) < 1)
            return FALSE;

        foreach ($readouts as $readout) {
            $queryOK = FALSE;

            if ($ommitCounter > self::MAX_OMMIT_INSERT_COUNT)
                return FALSE;

            if (array_key_exists('readout_time', $readout) || $ommitCounter > 0) {
                if (! array_key_exists('readout_time', $readout))
                    continue;

                $queryOK = $this->insertReadoutTime($dataTypeName, $readout);
                if ($queryOK === FALSE) {
                    $ommitCounter += 1;
                    continue;
                } else
                    $ommitCounter = 0;
            } else {
                if ($dataTypeName === 'santerno_readouts')
                    $queryOK = $this->insertPowerData($readout);
                else if ($dataTypeName === 'ds18b20_readouts')
                    $queryOK = $this->insertTemperatureData($readout);
                else if ($dataTypeName === 'purifier_readouts')
                    $queryOK = $this->insertPurifierData($readout);
                else if ($dataTypeName === 'tasmota_readouts')
                    $queryOK = $this->insertTasmotaData($readout);
                else
                    return FALSE;
            }

            if ($queryOK === FALSE)
                return FALSE;
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

        foreach ($keys as $dataTypeName) {
            if (strpos($dataTypeName, "_readouts") > 0) {
                if ($this->insertReadoutsToDB($dataTypeName, $json[$dataTypeName])) {
                    mysqli_commit($this->Connection);
                    echo "_" . $dataTypeName . "_inserted_ok_";
                } else
                    mysqli_rollback($this->Connection);
            }
        }
    }
}

?>
