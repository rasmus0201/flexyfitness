<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api;
use App\Helpers\ApiCache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->get('token');

        $cache = new ApiCache($token, storage_path(env('API_CACHE')));

        if ($data = $cache->getFile('activities.json')) {
            $data['cached'] = true;
            return response()->json($data, $data['status']);
        }

        $api = new Api($token);
        $data = [
            'status'    => 200,
            'cached'    => false,
            'msg'       => 'Success',
            'data'      => []
        ];

        $activities = $api->activities();
        if (!empty($activities)) {
            $data['data'] = $activities;
        }

        $cache = new ApiCache($token, storage_path(env('API_CACHE')));

        // Cache response
        $cache->cacheFile('activities.json', $data);

        return response()->json($data, $data['status']);
    }

    public function join(Request $request)
    {
        $activityId = $request->get('activityId');
        $token = $request->get('token');

        if (!is_string($activityId) || !ctype_digit($activityId)) {
            return response()->json(['status' => 404, 'msg' => 'Not found'], 404);
        }

        $cache = new ApiCache($token, storage_path(env('API_CACHE')));

        //We can't "trust" the cached json file nor
        //the activities, so just prune everything
        $cache->deleteFile('activities.json');
        $cache->deleteFile('calendar-*.json');

        $api = new Api($token);

        $data = $api->join($activityId);
        $data['cached'] = false;

        return response()->json($data, $data['status']);
    }

    public function leave(Request $request)
    {
        $bookingId = $request->get('bookingId');
        $token = $request->get('token');

        if (!is_string($bookingId) || !ctype_digit($bookingId)) {
            return response()->json(['status' => 404, 'msg' => 'Not found'], 404);
        }

        $cache = new ApiCache($token, storage_path(env('API_CACHE')));

        // We can't "trust" the cached json file nor
        // the activities, so just prune everything
        $cache->deleteFile('activities.json');
        $cache->deleteFile('calendar-*.json');

        $api = new Api($token);

        $data = $api->leave($bookingId);
        $data['cached'] = false;

        return response()->json($data, 200);
    }
}
