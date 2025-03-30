<?php

require_once 'SqlRequest.php';

class UsersGetter extends SqlRequest
{
    public function getData()
    {
        $query = 'SELECT user_name, api_hash FROM users';

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                return false;
            }

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                if (array_key_exists('user_name', $row) && array_key_exists('api_hash', $row)) {
                    $rows[] = array('user_name' => $row['user_name'], 'api_hash' => substr($row['api_hash'], 0, 14));
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
