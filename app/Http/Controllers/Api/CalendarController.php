<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api;
use App\Helpers\Helper;
use App\Helpers\ApiCache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function index(Request $request, $timestamp = null)
    {
        try {
            $token = $request->get('token');
            $startOfWeek = Helper::startOfWeek($timestamp);

            $cache = new ApiCache($token, storage_path(env('API_CACHE')));

            $file = 'calendar-'.$startOfWeek->getTimestamp().'.json';
            if ($data = $cache->getFile($file)) {
                $data['cached'] = true;
                return response()->json($data, $data['status']);
            }

            // Max 2 years back
            if ($startOfWeek->format('Y') < (date('Y') - 2)) {
                throw new \Exception('Date too long ago');
            }

            // Max 2 years above
            if ($startOfWeek->format('Y') > (date('Y') + 2)) {
                throw new \Exception('Date too long in the future');
            }

            $api = new Api($token);

            $res = [
                'status'    => 200,
                'cached'    => false,
                'msg'       => 'Success retrieving calendar for date: '.$startOfWeek->format('d/m/Y'),
                'data'      => $api->week($startOfWeek->format('m/d/Y'))
            ];

            // Cache response
            $cache->cacheFile($file, $res);

            return response()->json($res, $res['status']);
        } catch (\Exception $e) {
            return response()->json(['status' => 422, 'msg' => 'Date is wrong.'], 422);
        }
    }
}
