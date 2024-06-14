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

if (!function_exists("errPublicHolidayAssigned")) {
    function errPublicHolidayAssigned($internalMsg = "", $status = null)
    {
        error(400, "Public Holiday Already assigned !", $internalMsg, $status);
    }
}

if (!function_exists("errPublicHolidayNotFound")) {
    function errPublicHolidayNotFound($internalMsg = "", $status = null)
    {
        error(404, "Public Holiday not found !", $internalMsg, $status);
    }
}

if (!function_exists("errLeaveMoteThanAWeek")) {
    function errLeaveMoteThanAWeek($internalMsg = "", $status = null)
    {
        error(400, "Cannot request leave more than a week !", $internalMsg, $status);
    }
}
