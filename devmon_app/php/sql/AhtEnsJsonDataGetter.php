<?php

require_once 'SqlRequest.php';

class AhtEnsJsonDataGetter extends SqlRequest
{
    public function getData($dateFrom, $dateTo)
    {
        $dateFrom = sprintf('%s 00:00:00', $dateFrom);
        $dateTo = sprintf('%s 23:59:59', $dateTo);
        $userName = $this->getUserData("user_name");

        $query_purifier = 'SELECT L.readout_time, D.dev_name, A.aqi, A.eco2, A.tvoc, A.temperature, A.humidity FROM aht_ens_data_readings A
            LEFT JOIN data_logs L on (L.data_id = A.data_id)
            LEFT JOIN devices D on (D.device_id = A.device_id)
            WHERE L.readout_time BETWEEN ? AND ? AND D.user_id = (SELECT user_id FROM users WHERE user_name = ?)';

        if ($stmt = mysqli_prepare($this->Connection, $query_purifier)) {
            mysqli_stmt_bind_param($stmt, "sss", $dateFrom, $dateTo, $userName);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                return false;
            }

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $dev_name = $row['dev_name'];
                $readout_time = $row['readout_time'];

                $rows[$readout_time][$dev_name . '_aqi'] = $row['aqi'];
                $rows[$readout_time][$dev_name . '_eco2'] = $row['eco2'];
                $rows[$readout_time][$dev_name . '_tvoc'] = $row['tvoc'];
                $rows[$readout_time][$dev_name . '_temperature'] = $row['temperature'];
                $rows[$readout_time][$dev_name . '_humidity'] = $row['humidity'];
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows)) {
                return $rows;
            }
        }

        return false;
    }
}
