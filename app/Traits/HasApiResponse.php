<?php

namespace App\Traits;

trait HasApiResponse
{
    /**
     * Return a JSON response.
     * Fills default values for response
     *
     * @param  array  $json
     * @param  integer $status
     *
     * @return \Illuminate\Http\Response
     */
    protected function response($json = [], $status = null)
    {
        $json = array_merge([
            'status'    => 200,
            'msg'       => 'Success',
            'data'      => []
        ], $json);

        if ($status) {
            $json['status'] = $status;
        }

        return response()->json($json, $json['status']);
    }

    /**
     * Throw JSON unauthorized response
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthorized()
    {
        return $this->response([
            'status' => 401,
            'msg' => 'Unauthorized'
        ]);
    }
}
