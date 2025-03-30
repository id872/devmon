<?php

require_once 'SqlRequest.php';

class DataTypesGetter extends SqlRequest
{
    public function getData()
    {
        $query = 'SELECT dt_name, dt_description FROM dev_data_type ORDER BY dt_id';

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                return false;
            }

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                if (array_key_exists('dt_name', $row) && array_key_exists('dt_description', $row)) {
                    $rows[] = array('dt_name' => $row['dt_name'], 'dt_description' => $row['dt_description']);
                }
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows)) {
                return $rows;
            }
        }
        return false;
    }
}
