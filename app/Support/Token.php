<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

class Token
{
    /**
     * Method to encrypt token
     *
     * @param  string $token
     *
     * @return string
     */
    public static function encrypt($token)
    {
        return Crypt::encrypt($token);
    }

    /**
     * Method to decrypt token
     *
     * @param  string $token
     *
     * @return string
     */
    public static function decrypt($token)
    {
        return Crypt::decrypt($token);
    }
}
