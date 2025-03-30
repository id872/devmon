<?php

require_once 'sql/DeviceDataSaver.php';

function decodeAES($key, $encData)
{
    $iv_enc = base64_decode($encData);

    if ($iv_enc === false) {
        return null;
    }

    $pass = hash('sha256', $key, true);

    $iv = substr($iv_enc, 0, 16);
    $enc = substr($iv_enc, 16);

    if (strlen($iv) == 16 && strlen($enc) >= 16) {
        return zlib_decode(openssl_decrypt($enc, 'AES-256-CFB8', $pass, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv));
    } else {
        return null;
    }
}

function decodeToJson($key, $encData)
{
    $jsonData = json_decode(decodeAES($key, $encData), true);

    if (is_array($jsonData) && array_key_exists("username", $jsonData) && array_key_exists("user_password", $jsonData)) {
        return $jsonData;
    }

    return null;
}

function checkUserPassword($json, $userHash)
{
    if (array_key_exists("user_password", $json)) {
        if (password_verify($json["user_password"], $userHash)) {
            return true;
        }
    }

    return false;
}

if (array_key_exists("hash", $_POST) && array_key_exists("data", $_POST)) {
    $hash = base64_decode(trim($_POST["hash"]));

    if (strlen($hash) != 40) {
        exit(1);
    }

    $sql = new DeviceDataSaver($hash);
    $auth = $sql->getAuth();

    if (! empty(array_values($auth))) {
        $json = decodeToJson($auth["api_key"], trim($_POST["data"]));

        if ($json !== null && checkUserPassword($json, $auth["user_password_hash"])) {
            $sql->addData($json);
        }
    }
}
