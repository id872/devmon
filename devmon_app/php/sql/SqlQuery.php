<?php

class SqlQuery
{

    public static function updatePowerDayStats($dbCon)
    {
        if (! $dbCon)
            return;
        $query = "REPLACE INTO power_day_stats(user_id, device_id, day_production, kwh)
            SELECT user_id, device_id, date_format(readout_time, '%Y-%m-%d'), 
            ROUND((SUM(ac_power) / COUNT(ac_power)) * TIMESTAMPDIFF(SECOND, MIN(readout_time), MAX(readout_time)) / (60 * 60) / 1000, 2)
            FROM view_power_logs 
            WHERE date_format(readout_time, '%Y-%m-%d') 
            BETWEEN (SELECT date_format(max(day_production), '%Y-%m-%d') FROM power_day_stats)
            AND date_format(CURRENT_DATE, '%Y-%m-%d')
            GROUP BY device_id, date_format(readout_time, '%Y-%m-%d');";
        mysqli_query($dbCon, $query);
    }
}
?>
