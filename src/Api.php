<?php
/**
 * Created by PhpStorm.
 * User: panuka
 * Date: 02.11.17
 * Time: 11:54
 */

namespace Panuka\Waspace;

Class Api {
    private $token = null;

    const OC_GET_GENERAL_INFO = 'Get general info';
    const OC_SIGNIN = 'Sign in';
    const OC_GETFOLDERS = 'Get folders';

    function __construct($username, $password) {
        if (is_null($this->token)) $this->token = $this->getToken($username, $password);
    }

    function getBalance() {
        return $this->getFrom(self::OC_GET_GENERAL_INFO, 'Balance');
    }

    private function getToken($username, $password) {
        $data = $this->request(self::OC_SIGNIN, ['Mail' => $username,
                                                 'Password' => $password,
                                                 'Remember' => true]);
        return $data['Token'];
    }

    private function request($method, $data = []) {
        if (!isset($data['Token'])) $data['Token'] = $this->token;
        $method = urlencode($method);
        $data = json_encode($data);
        $response = json_decode(file_get_contents("http://api.waspace.net/{$method}/{$data}"), true);
        if ($response['Status'] == 'Success') {
            $response = $response['Data'];
        } else {
            throw new \Exception("Error API answer: {$response['Status']}");
        }

        return $response;
    }

    public function getFrom($from, $field) {
        $data = $this->request($from);
        if (isset($data[$field])) {
            $data = $data[$field];
        } else {
            $data = null;
        }
        return $data;
    }

}