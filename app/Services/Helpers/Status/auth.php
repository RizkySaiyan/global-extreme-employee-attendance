<?php

if (!function_exists("errInvalidCredentials")) {
    function errInvalidCredentials($internalMsg = "", $status = null)
    {
        error(404, "Invalid username and password!", $internalMsg, $status);
    }
}

if (!function_exists("errUnauthorized")) {
    function errUnauthorized($internalMsg = "", $status = null)
    {
        error(401, "User unauthorized!", $internalMsg, $status);
    }
}
