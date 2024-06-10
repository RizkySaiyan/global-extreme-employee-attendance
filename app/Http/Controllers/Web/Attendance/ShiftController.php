<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Component\ComponentAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\ShiftRequest;
use App\Models\Attendance\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    //
    public function get(Request $request){
        $shift = Shift::getOrPaginate($request);

        return success($shift);
    }

    public function create(ShiftRequest $request){
        $algo = new ComponentAlgo();
        
        return $algo->createBy(Shift::class, $request);
    }

    public function update($id, ShiftRequest $request){
        $shift = Shift::find($id);
        
        if (!$shift) {
            errShiftNotFound();
        }

        $algo = new ComponentAlgo();
        return $algo->update($shift, $request);
    }

    public function delete($id){
        $shift = Shift::find($id);

        if(!$shift){
            errShiftNotFound();
        }

        $algo = new ComponentAlgo();
        return $algo->delete($shift);
    }
}
