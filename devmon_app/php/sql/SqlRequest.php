<?php

class SqlRequest
{
    const CONFIG_PATH = '../../../sql_db/db_config.ini';

    protected $Connection = null;

    protected $UserData = null;

    function __construct($userHash)
    {
        $this->Connection = $this->dbConnect();

        if (! $this->Connection) {
            exit(1);
        }

        $this->UserData = $this->initializeUserData($userHash);
    }

    function __destruct()
    {
        if ($this->Connection) {
            mysqli_close($this->Connection);
        }
    }

    private function dbConnect()
    {
        $configPath = realpath(self::CONFIG_PATH);
        if (!$configPath) {
            echo "Not found";
            return null;
        }

        $config = parse_ini_file($configPath);
        if ($config) {
            return mysqli_connect($config["servername"], $config["username"], $config["password"], $config["dbname"]);
        }

        return null;
    }

    private function initializeUserData($userHash)
    {
        if ($userHash === null) {
            return null;
        }

        $query = "SELECT D.dev_name, D.device_id, U.user_name, U.user_id, U.user_password_hash, U.api_key FROM users U 
            LEFT JOIN devices D on (D.user_id = U.user_id) where INSTR(api_hash, ?) > 0";

        if ($stmt = mysqli_prepare($this->Connection, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $userHash);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === false) {
                return null;
            }

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $rows[] = $row;
            }

            mysqli_stmt_close($stmt);

            if (! empty($rows)) {
                return $rows;
            }
        }

        return null;
    }

    protected function getUserData($key)
    {
        if (is_array($this->UserData) && array_key_exists(0, $this->UserData) && array_key_exists($key, $this->UserData[0])) {
            return $this->UserData[0][$key];
        }

        return null;
    }
}
