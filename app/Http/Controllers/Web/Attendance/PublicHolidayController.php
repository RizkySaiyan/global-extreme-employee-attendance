<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Algorithms\Attendance\PublicHolidayAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\PublicHolidayRequest;
use App\Models\Attendance\PublicHoliday;
use App\Parser\Attendance\PublicHolidayParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicHolidayController extends Controller
{
    public function get(Request $request)
    {
        //temporary, because i cant put orderBy in filter scopes
        $publicHoliday = PublicHoliday::filter($request)->orderBy($request->sortBy, $request->direction)->getOrPaginate($request);
        return success(PublicHolidayParser::get($publicHoliday));
    }

    public function create(PublicHolidayRequest $request)
    {
        $algo = new PublicHolidayAlgo();
        return $algo->create($request);
    }

    public function update($id, PublicHolidayRequest $request)
    {
        $publicHoliday = PublicHoliday::find($id);
        if (!$publicHoliday) {
            errPublicHolidayNotFound();
        }

        $algo = new PublicHolidayAlgo($publicHoliday);
        return $algo->update($request);
    }

    public function delete($id)
    {
        $publicHoliday = PublicHoliday::find($id);
        if (!$publicHoliday) {
            errPublicHolidayNotFound();
        }

        $algo = new PublicHolidayAlgo($publicHoliday);
        return $algo->delete();
    }

    public function assignPublicHoliday($id)
    {
        $publicHoliday = PublicHoliday::find($id);
        if (!$publicHoliday) {
            errPublicHolidayNotFound();
        }

        $algo = new PublicHolidayAlgo($publicHoliday);
        return $algo->assignPublicHoliday();
    }
}
