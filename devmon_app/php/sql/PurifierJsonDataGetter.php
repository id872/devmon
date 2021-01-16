<?php
require_once 'SqlRequest.php';

class PurifierJsonDataGetter extends SqlRequest
{

    public function getData($dateFrom, $dateTo)
    {
        $dateFrom = sprintf('%s 00:00:00', $dateFrom);
        $dateTo = sprintf('%s 23:59:59', $dateTo);
        $userName = $this->getUserData("user_name");

        $query_purifier = 'SELECT L.readout_time, D.dev_name, P.aqi, P.humidity, P.temperature, P.fan_rpm FROM purifier_data_readings P
            LEFT JOIN data_logs L on (L.data_id = P.data_id)
            LEFT JOIN devices D on (D.device_id = P.device_id)
            WHERE L.readout_time BETWEEN ? AND ? AND D.user_id = (SELECT user_id FROM users WHERE user_name = ?)';

        if ($stmt = mysqli_prepare($this->Connection, $query_purifier)) {
            mysqli_stmt_bind_param($stmt, "sss", $dateFrom, $dateTo, $userName);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === FALSE)
                return FALSE;

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $dev_name = $row['dev_name'];
                $readout_time = $row['readout_time'];

                $rows[$readout_time][$dev_name . '_aqi'] = $row['aqi'];
                $rows[$readout_time][$dev_name . '_humidity'] = $row['humidity'];
                $rows[$readout_time][$dev_name . '_temperature'] = $row['temperature'];
                $rows[$readout_time][$dev_name . '_fan_rpm'] = $row['fan_rpm'];
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows))
                return $rows;
        }

        return FALSE;
    }
}

?>
