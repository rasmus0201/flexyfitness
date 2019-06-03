<?php

namespace App\Flexybox;

use App\User;

class FlexyfitnessApi
{
    protected $cmd = 'python3';
    protected $checkAuth;
    protected $getBookings;
    protected $getWeek;
    protected $joinActivity;
    protected $leaveActivity;

    protected $username;
    protected $password;

    public function __construct(User $user)
    {
        $this->checkAuth        = bin_path() . '/checkAuth.py';
        $this->getBookings      = bin_path() . '/getBookings.py';
        $this->getWeek          = bin_path() . '/getWeek.py';
        $this->joinActivity     = bin_path() . '/joinActivity.py';
        $this->leaveActivity    = bin_path() . '/leaveActivity.py';

        $this->username = escapeshellarg($user->username);
        $this->password = escapeshellarg($user->api_password);
    }

    public function auth()
    {
        $output = $this->execute($this->cmd .' '. $this->checkAuth);

        $json = $this->tryJsonDecode($output[0]);

        if (empty($json) || !isset($json['status'])) {
            return [
                'status'    => 401,
                'msg'       => 'Forkert brugernavn/adgangskode',
                'data'      => []
            ];
        }

        return $json;
    }

    public function week($date = '')
    {
        $output = $this->execute($this->cmd .' '. $this->getWeek, escapeshellarg($date));

        return $this->tryJsonDecode($output[0]);
    }

    public function activities()
    {
        $output = $this->execute($this->cmd .' '. $this->getBookings);

        return $this->tryJsonDecode($output[0]);
    }

    public function join($id)
    {
        $output = $this->execute($this->cmd .' '. $this->joinActivity, escapeshellarg($id));

        return $this->tryJsonDecode($output[0]);;
    }

    public function leave($id)
    {
        $output = $this->execute($this->cmd .' '. $this->leaveActivity, escapeshellarg($id));

        return $this->tryJsonDecode($output[0]);
    }

    private function execute($cmdStart = '', $cmdEnd = '')
    {
        $output = [];

        $cmd = $cmdStart.' '.$this->username.' '.$this->password.' '.$cmdEnd.' 2>&1';

        exec($cmd, $output);

        return $output;
    }

    private function tryJsonDecode($arr)
    {
        if (!isset($arr)) {
            return [];
        }

        if (empty($arr)) {
            return [];
        }

        try {
            $json = json_decode($arr, true);
        } catch (\Exception $e) {
            return [];
        }

        return $json;
    }
}
