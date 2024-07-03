<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\CorrectionAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\CorrectionRequest;
use App\Models\Attendance\Correction;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function get(Request $request)
    {
        $corrections = Correction::getOrPaginate($request);

        return success($corrections);
    }

    public function create(CorrectionRequest $request)
    {
        $algo = new CorrectionAlgo();
        return $algo->create($request);
    }

    public function approves(Request $request, $id)
    {
        $corrections = Correction::find($id);
        if (!$corrections) {
            errAttendanceCorrectionNotFound();
        }

        $algo = new CorrectionAlgo($corrections);
        return $algo->approves($request);
    }

    public function disapproves(Request $request, $id)
    {
        $corrections = Correction::find($id);
        if (!$corrections) {
            errAttendanceCorrectionNotFound();
        }

        $algo = new CorrectionAlgo($corrections);
        return $algo->disapproves($request);
    }
}
