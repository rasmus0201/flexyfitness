<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;

class DateController extends Controller
{
    public function index()
    {
        $datetime = Helper::date();

        return response()->json([
            'status'    => 200,
            'cached'    => false,
            'msg'       => 'Success',
            'data'      => [
                'timestamp' => $datetime->getTimestamp(),
                'datetime'  => $datetime->format('Y/m/d H:i:s'),
                'date'  => $datetime->format('Y/m/d'),
                'weekDay'  => (int)$datetime->format('w'),
            ]
        ], 200);
    }
}
