<?php
if (!function_exists("errShiftNotFound")) {
    function errShiftNotFound($internalMsg = "", $status = null)
    {
        error(404, "Shift Not Found!", $internalMsg, $status);
    }
}

if (!function_exists("errAttendanceDateExist")) {
    function errAttendanceDateExist($internalMsg = "", $status = null)
    {
        error(404, "Date already assigned !", $internalMsg, $status);
    }
}
