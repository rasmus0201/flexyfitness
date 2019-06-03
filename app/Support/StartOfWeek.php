<?php

namespace App\Support;

use Carbon\Carbon;

class StartOfWeek
{
    /**
     * Method to get the start of a week by a given date.
     * Default parameters is optimized for a UNIX Epoch
     *
     * @param  mixed $fromDate
     * @param  string $fromFormat
     *
     * @return Carbon
     */
    public static function get($fromDate = null, $fromFormat = 'U')
    {
        $date = self::toCarbonDate($fromDate, $fromFormat);

        $date->setTime(0, 0, 0);

        if ($date->format('N') == 1) {
            // If the date is already a Monday, return it as-is
            return Carbon::instance($date);
        }

        // Otherwise, return the date of the nearest Monday in the past
        // This includes Sunday in the previous week instead of it being the start of a new week
        return Carbon::instance($date->modify('last monday'));
    }

    /**
     * Turn a string into a date by timestamp.
     * Default date is now
     *
     * @param  mixed $date
     * @param  string $format
     *
     * @return Carbon
     */
    private static function toCarbonDate($date = null, $format = 'Y-m-d H:i:s')
    {
        if (is_null($date)) {
            return Carbon::now();
        }

        if ($date instanceof Carbon) {
            return clone $date;
        }

        if ($date instanceof \DateTime) {
            return Carbon::instance($date);
        }

        return Carbon::createFromFormat($format, $date);
    }
}
