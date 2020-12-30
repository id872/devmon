<?php

require_once('SqlRequest.php'); 

class TasmotaJsonDataGetter extends SqlRequest
{
    public function getTasmotaData($dateFrom, $dateTo)
    {
        $dateFrom = sprintf('%s 00:00:00', $dateFrom);
        $dateTo = sprintf('%s 23:59:59', $dateTo);
        $userName = $this->getUserData("user_name");

        $query_purifier = 'SELECT L.readout_time, D.dev_name, T.ac_power, T.ac_voltage, T.ac_current FROM tasmota_data_readings T
            LEFT JOIN data_logs L on (L.data_id = T.data_id)
            LEFT JOIN devices D on (D.device_id = T.device_id)
            WHERE L.readout_time BETWEEN ? AND ? AND D.user_id = (SELECT user_id FROM users WHERE user_name = ?)';

        if ($stmt = mysqli_prepare($this->Connection, $query_purifier))
        {
            mysqli_stmt_bind_param($stmt, "sss", $dateFrom, $dateTo, $userName);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === FALSE)
            {
                return FALSE;
            }

            $rows = array();

            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $dev_name = $row['dev_name'];
                $readout_time = $row['readout_time'];

                $rows[$readout_time][$dev_name.'_ac_power'] = $row['ac_power'];
                $rows[$readout_time][$dev_name.'_ac_voltage'] = $row['ac_voltage'];
                $rows[$readout_time][$dev_name.'_ac_current'] = $row['ac_current'];
            }

            mysqli_stmt_close($stmt);

            if (!empty($rows))
            {
                return $rows;
            }
        }

        return FALSE;
    }
}

?>
