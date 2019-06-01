<?php namespace App\Helpers;

use App\Helpers\Helper;

class ApiCache
{

    private $token;
    private $cacheFolder;
    private $userDir = false;

    function __construct($token, $cacheFolder)
    {
        $this->token = $token;
        $this->cacheFolder = rtrim($cacheFolder, '/') . '/';

        $this->setUser();
    }

    function userExists()
    {
        return ($this->userDir !== false) ? true : false;
    }

    function setUser()
    {
        $dirs = glob($this->cacheFolder.'*');

        $found = false;

        //This could potentially become very slow
        //Because it as to check every dir and hash it with bcrypt
        foreach($dirs as $dir) {
            $checkUserDir = str_replace($this->cacheFolder, '', $dir);

            if (Helper::verify($checkUserDir, $this->token)) {
                $found = $checkUserDir;
                break;
            }
        }

        $this->userDir = $found;
    }

    function createUser()
    {
        if ($this->userDir === false) {
            $this->userDir = Helper::hash($this->token);

            $path = $this->cacheFolder . $this->userDir;

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    function deleteUserDir()
    {
        if ($this->userDir === false) {
            return true;
        }

        $dir = $this->cacheFolder . $this->userDir;
        if (file_exists($dir) && is_dir($dir)) {
            $this->deleteFile('*.json');
            return rmdir($dir);
        }

        return true;
    }

    function deleteFile($file)
    {
        if ($this->userDir === false) {
            return false;
        }

        $files = $this->cacheFolder . $this->userDir . '/' . $file;
        foreach(glob($files) as $f) {
            if ($f == '.' || $f == '..') {
                continue;
            }

            if (file_exists($f) && is_file($f)) {
                unlink($f);
            }
        }

        return true;
    }

    function getFile($file, $maxAge = '20 min')
    {
        if ($this->userDir === false) {
            return false;
        }

        $data = false;
        $path = $this->cacheFolder . $this->userDir . '/' . $file;
        if ($this->userDir !== false && file_exists($path)) {

            $format = 'm-d-Y H:i:s';
            $timeAgo = date($format, strtotime('-'.$maxAge));
            $maxDate = \DateTime::createFromFormat($format, $timeAgo);

            $timeUpdated = date($format, filectime($path));
            $updated = \DateTime::createFromFormat($format, $timeUpdated);

            if ($maxDate > $updated) {
                $this->deleteFile($file);

                return false;
            }

            $data = json_decode(file_get_contents($path), true);
        }

        return $data;
    }

    function cacheFile($file, $data)
    {
        //Cache response
        $this->createUser();

        $path = $this->cacheFolder . $this->userDir;

        if ($handle = fopen($path . '/' . $file, 'w')) {
            fwrite($handle, json_encode($data));
            fclose($handle);
        }

        return true;
    }
}
