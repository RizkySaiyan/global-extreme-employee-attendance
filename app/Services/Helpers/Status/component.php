<?php

if (!function_exists("errCompanyOfficeNotFound")) {
    function errCompanyOfficeNotFound($internalMsg = "", $status = null)
    {
        error(404, "Company Office Not Found!", $internalMsg, $status);
    }
}

if (!function_exists("errCompanyOfficeHasDepartment")) {
    function errCompanyOfficeHasDepartment($internalMsg = "", $status = null)
    {
        error(400, "Company Office has that department!", $internalMsg, $status);
    }
}


if (!function_exists("errDepartmentNotFound")) {
    function errDepartmentNotFound($internalMsg = "", $status = null)
    {
        error(404, "Department Not Found!", $internalMsg, $status);
    }
}

if (!function_exists("errDepartmentDelete")) {
    function errDepartmentDelete($internalMsg = "", $status = null)
    {
        error(400, "Cannot Delete Department because it has Company Office!", $internalMsg, $status);
    }
}
