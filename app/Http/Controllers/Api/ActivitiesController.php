<?php

namespace App\Http\Controllers\Api;

use App\Flexybox\FlexyfitnessApi;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    /**
     * Get a users bookings
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check if any cached activities
        if ($cache = $request->user()->bookings()->latest()->first()) {
            if ($cache->isFresh()) {
                return $this->response(['data' => $cache->data]);
            }

            // Delete if expired
            $cache->delete();
        }

        $api = new FlexyfitnessApi($request->user());

        $bookings = $api->activities();

        // Cache activities
        $request->user()->bookings()->create([
            'data' => $bookings
        ]);

        return $this->response(['data' => $bookings]);
    }

    /**
     * Join an activity
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function join(Request $request)
    {
        $this->validate($request, [
            'activityId' => 'required|integer'
        ]);

        // Delete activities cache
        $request->user()->bookings()->delete();

        // Delete calendar cache
        $request->user()->calendarWeeks()->delete();

        $api = new FlexyfitnessApi($request->user());

        return $this->response($api->join($request->get('activityId')));
    }

    /**
     * Leave an activity
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function leave(Request $request)
    {
        $this->validate($request, [
            'bookingId' => 'required|integer'
        ]);

        // Delete activities cache
        $request->user()->bookings()->delete();

        // Delete calendar cache
        $request->user()->calendarWeeks()->delete();

        $api = new FlexyfitnessApi($request->user());

        return $this->response($api->leave($request->get('bookingId')));
    }
}
