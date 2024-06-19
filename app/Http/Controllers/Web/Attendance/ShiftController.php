<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\ShiftAlgo;
use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ShiftRequest;
use App\Models\Attendance\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    //
    public function get(Request $request)
    {
        $shift = Shift::getOrPaginate($request);
        return success($shift);
    }

    public function create(ShiftRequest $request)
    {
        $algo = new ShiftAlgo();
        return $algo->create($request);
    }

    public function update($id, ShiftRequest $request)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            errShiftNotFound();
        }

        $algo = new ShiftAlgo($shift);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            errShiftNotFound();
        }

        $algo = new ShiftAlgo($shift);
        return $algo->delete();
    }
}
