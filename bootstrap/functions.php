<?php

if (!function_exists('bin_path')) {
    /**
     * Get the path to the storage folder
     *
     * @return string
     */
    function bin_path()
    {
        return base_path() . '/bin';
    }
}
