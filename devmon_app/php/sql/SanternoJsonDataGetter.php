<?php

require_once 'SqlRequest.php';

class SanternoJsonDataGetter extends SqlRequest
{
    public function getData($dateDay)
    {
        $dateFrom = sprintf('%s 00:00:00', $dateDay);
        $dateTo = sprintf('%s 23:59:59', $dateDay);
        $userName = $this->getUserData("user_name");

        $query = 'SELECT L.readout_time, D.dev_name, P.ac_power, P.dc_current, P.dc_voltage, P.cpu_temperature, P.radiator_temperature,
        P.grid_voltage, P.grid_current, P.grid_frequency FROM power_data_readings P
            LEFT JOIN data_logs L on (L.data_id = P.data_id)
            LEFT JOIN devices D on (D.device_id = P.device_id)
            WHERE L.readout_time BETWEEN ? AND ? AND D.user_id = (SELECT user_id FROM users WHERE user_name = ?)
            ORDER BY L.readout_time, D.dev_name';

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
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

                $rows[$readout_time][$dev_name . '_datetime'] = $readout_time;
                $rows[$readout_time][$dev_name . '_ac_power'] = $row['ac_power'];
                $rows[$readout_time][$dev_name . '_dc_current'] = $row['dc_current'];
                $rows[$readout_time][$dev_name . '_dc_voltage'] = $row['dc_voltage'];
                $rows[$readout_time][$dev_name . '_cpu_temperature'] = $row['cpu_temperature'];
                $rows[$readout_time][$dev_name . '_radiator_temperature'] = $row['radiator_temperature'];
                $rows[$readout_time][$dev_name . '_grid_voltage'] = $row['grid_voltage'];
                $rows[$readout_time][$dev_name . '_grid_current'] = $row['grid_current'];
                $rows[$readout_time][$dev_name . '_grid_frequency'] = $row['grid_frequency'];
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows)) {
                return $rows;
            }
        }
        return false;
    }

    public function getMonthStatsData()
    {
        $userName = $this->getUserData("user_name");
        $query = 'SELECT (SELECT dev_name FROM devices WHERE device_id = p.device_id) AS dev_name, YEAR(day_production) AS y, MONTH(day_production) AS m, SUM(kwh) AS kwh FROM `power_day_stats` p WHERE p.user_id = (SELECT user_id FROM users WHERE user_name = ?) GROUP BY device_id, YEAR(day_production), MONTH(day_production) ORDER BY y DESC, m, dev_name ASC';

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $userName);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                return false;
            }

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $devName = $row['dev_name'];
                $yearMonth = sprintf('%d_%d', $row['y'], $row['m']);

                $rows[$row['y']][$yearMonth][$devName . '_kwh'] = $row['kwh'];
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows)) {
                return $rows;
            }
        }
        return false;
    }
}
