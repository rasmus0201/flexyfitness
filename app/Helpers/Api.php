<?php namespace App\Helpers;

class Api {
    protected $cmd = 'python3';
    protected $checkAuth;
    protected $getBookings;
    protected $getWeek;
    protected $joinActivity;
    protected $leaveActivity;

    protected $username;
    protected $password;

    public function __construct($token)
    {
        $this->checkAuth        = bin_path() . '/checkAuth.py';
        $this->getBookings      = bin_path() . '/getBookings.py';
        $this->getWeek          = bin_path() . '/getWeek.py';
        $this->joinActivity     = bin_path() . '/joinActivity.py';
        $this->leaveActivity    = bin_path() . '/leaveActivity.py';

        if (empty($token)){
            throw new \Exception('Missing parameter. "token" must not be empty', 1);
        } else if (count(explode(':', base64_decode($token))) !== 2) {
            throw new \Exception('Parameter "token" should contain exactly a base64 encoded string matching: username:password', 1);
        }

        list($username, $password) = explode(':', base64_decode($token));

        $this->username = escapeshellarg($username);
        $this->password = escapeshellarg($password);
    }

    public function isAuth()
    {
        return $this->auth()['status'] === 200;
    }

    public function auth()
    {
        $output = $this->execute($this->cmd .' '. $this->checkAuth);

        $json = $this->tryJsonDecode($output[0]);

        $response = array(
            'status'    => 401,
            'msg'       => 'Forkert brugernavn/adgangskode',
            'data'      => [],
        );

        if (!$json) {
            return $response;
        }

        if (!isset($json['status'])) {
            return $response;
        }

        return $json;
    }

    public function week($date = '')
    {
        $output = $this->execute($this->cmd .' '. $this->getWeek, escapeshellarg($date));

        $json = $this->tryJsonDecode($output[0]);

        if (!$json) {
            return [];
        }

        return $json;
    }

    public function activities()
    {
        $output = $this->execute($this->cmd .' '. $this->getBookings);

        $json = $this->tryJsonDecode($output[0]);

        if (!$json) {
            return [];
        }

        return $json;
    }

    public function join($id)
    {
        $output = $this->execute($this->cmd .' '. $this->joinActivity, escapeshellarg($id));

        $json = $this->tryJsonDecode($output[0]);

        if (!$json) {
            return [];
        }

        return $json;
    }

    public function leave($id)
    {
        $output = $this->execute($this->cmd .' '. $this->leaveActivity, escapeshellarg($id));

        $json = $this->tryJsonDecode($output[0]);

        if (!$json) {
            return [];
        }

        return $json;
    }

    private function execute($cmdStart = '', $cmdEnd = '')
    {
        $output = array();

        $cmd = $cmdStart.' '.$this->username.' '.$this->password.' '.$cmdEnd.' 2>&1';

        exec($cmd, $output);

        return $output;
    }

    private function tryJsonDecode($arr)
    {
        if (!isset($arr)) {
            return null;
        }

        if (empty($arr)) {
            return null;
        }

        try {
            $json = json_decode($arr, true);

            return $json;
        } catch (\Exception $e) {
            return null;
        }
    }
}
