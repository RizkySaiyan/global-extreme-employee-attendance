<?php

if (!function_exists("errEmployeeNotFound")) {
    function errEmployeeNotFound($internalMsg = "", $status = null)
    {
        error(404, "Employee Not Found!", $internalMsg, $status);
    }
}

if (!function_exists("errEmployeeUserAdmin")) {
    function errEmployeeUserAdmin($internalMsg = "", $status = null)
    {
        error(404, "Employee is already an admin", $internalMsg, $status);
    }
}

if (!function_exists("errEmployeeResignExist")) {
    function errEmployeeResignExist($internalMsg = "", $status = null)
    {
        error(404, "Employee already submit resign!", $internalMsg, $status);
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

if (!function_exists("errOldPasswordNotMatch")) {
    function errOldPasswordNotMatch($internalMsg = "", $status = null)
    {
        error(500, "Old password wrong !", $internalMsg, $status);
    }
}