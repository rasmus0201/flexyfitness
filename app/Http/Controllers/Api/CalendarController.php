<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Support\StartOfWeek;
use Illuminate\Http\Request;
use App\Flexybox\FlexyfitnessApi;

class CalendarController extends Controller
{
    /**
     * Get a calendar week for user
     *
     * @param  Request $request
     * @param  string $timestamp
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $timestamp = null)
    {
        $startOfWeek = StartOfWeek::get($timestamp);

        $request->request->add([
            'min_date' => Carbon::now()->subDays(360),
            'max_date' => Carbon::now()->addDays(360),
            'date' => clone $startOfWeek
        ]);

        $this->validate($request, [
            'date' => 'required|date|after:min_date|before:max_date'
        ]);

        $force = $request->get('force') ?? false;

        // Check if any cached calendar weeks
        $query = $request->user()->calendarWeeks()->where('week', $startOfWeek);
        if ($cache = $query->latest()->first()) {
            if ($force === false && $cache->isFresh()) {
                return $this->response(['data' => $cache->data]);
            }

            // Delete if expired or force refresh
            $cache->delete();
        }

        $api = new FlexyfitnessApi($request->user());

        $calendarWeek = $api->week($startOfWeek->format('m/d/Y'));

        // Cache calendar week
        $request->user()->calendarWeeks()->create([
            'week' => $startOfWeek,
            'data' => $calendarWeek
        ]);

        return $this->response([
            'msg' => 'Success retrieving calendar for date: '.$startOfWeek->format('d/m/Y'),
            'data' => $calendarWeek
        ]);
    }
}
