<?php

namespace App\Traits;

use Carbon\Carbon;

trait CanExpire
{
    /**
     * Check if model is expired.
     * Default lifetime is 7 days.
     *
     * @param  integer $maxAge
     *
     * @return boolean
     */
    public function isExpired($maxAge = 604800)
    {
        return Carbon::now()->subSeconds($maxAge)->gt($this->created_at);
    }

    /**
     * If the model hasn't expired it's fresh.
     *
     * @return boolean
     */
    public function isFresh()
    {
        return !$this->isExpired();
    }
}
