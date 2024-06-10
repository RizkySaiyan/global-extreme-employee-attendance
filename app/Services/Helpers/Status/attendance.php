<?php
//Shift
if (!function_exists("errShiftNotFound")) {
    function errShiftNotFound($internalMsg = "", $status = null)
    {
        error(404, "Shift Not Found!", $internalMsg, $status);
    }
}
