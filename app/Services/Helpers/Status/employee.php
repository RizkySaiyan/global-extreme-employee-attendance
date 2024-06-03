<?php

if (!function_exists("errEmployeeNotFound")) {
    function errEmployeeNotFound($internalMsg = "", $status = null)
    {
        error(404, "Employee Not Found!", $internalMsg, $status);
    }
}


if (!function_exists("errEmployeeSiblingsSave")) {
    function errEmployeeSiblingsSave($internalMsg = "", $status = null)
    {
        error(500, "Unable to save siblings!", $internalMsg, $status);
    }
}

if (!function_exists("errEmployeeUserSave")) {
    function errEmployeeUserSave($internalMsg = "", $status = null)
    {
        error(500, "Unable to save employee user!", $internalMsg, $status);
    }
}