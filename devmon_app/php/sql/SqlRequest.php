<?php

class SqlRequest
{
    const CONFIG_PATH = '../../../../m_cfg/config.ini'; 

    protected $Connection = NULL; 
    protected $UserData = NULL;

    function __construct($userHash)
    {
        $this->Connection = $this->dbConnect();

        if (!$this->Connection)
        {
            exit(1);
        }

        $this->UserData = $this->initializeUserData($userHash);

        if (!$this->UserData)
        {
            exit(1);
        }
    }

    function __destruct()
    {
        if ($this->Connection)
        {
            mysqli_close($this->Connection);
        }
    }

    private function dbConnect()
    {
        $config = parse_ini_file(self::CONFIG_PATH);
        if ($config)
        {
            return mysqli_connect($config["servername"], $config["username"],
                $config["password"], $config["dbname"]);
        }

        return NULL;
    }

    private function initializeUserData($userHash)
    {
        $query = "SELECT D.dev_name, D.device_id, U.user_name, U.user_id, U.user_password_hash, U.api_key FROM users U 
            LEFT JOIN devices D on (D.user_id = U.user_id) where INSTR(api_hash, ?) > 0";

        if ($stmt = mysqli_prepare($this->Connection, $query))
        {
            mysqli_stmt_bind_param($stmt, "s", $userHash);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result === FALSE)
            {
                return NULL;
            }

            $rows = array();

            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            {
                $rows[] = $row;
            }

            mysqli_stmt_close($stmt);

            if (!empty($rows))
            {
                return $rows;
            }
        }

        return NULL;
    }

    protected function getUserData($key)
    {
        if (array_key_exists($key, $this->UserData[0]))
        {
            return $this->UserData[0][$key];
        }

        return NULL;
    }
}

?>
