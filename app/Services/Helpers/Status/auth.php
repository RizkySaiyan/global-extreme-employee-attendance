<?php

if (!function_exists("errInvalidCredentials")) {
    function errInvalidCredentials($internalMsg = "", $status = null)
    {
        error(404, "Invalid username and password!", $internalMsg, $status);
    }
}
