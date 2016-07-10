<?php
/*
 * Gomo Campaign Handler
 */
define('__HANDLER_ROOT__', dirname(dirname(__FILE__)));
require_once(__HANDLER_ROOT__.'/adapters/api.php');

class GomoHandler extends ApiAdapter
{
    public function __construct($url, $user, $pass, $subType)
    {
        $this->getAction($subType);
        parent::__construct($url);
        $this->sessionId = $this->getApiSession($user, $pass);
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    private function getApiSession($username, $password)
    {
        // Returns a session ID
        $fields = array(
            'action'   => 'getSession',
            'user'     => $username,
            'password' => $password
        );

        $output = $this->callApi($fields);
        return $output->sessionid;
    }

    public function getAllKnownIdsByMobile($mobile)
    {
        // Returns an array of known IDs by mobile
        $fields = array(
            'sessionid' => "$this->sessionId",
            'action'    => "getUserIDfromMobile",
            'mobile'    => $mobile
        );
        $output = $this->callApi($fields);
        return explode(",", $output->id);
    }

    public function getAction($subType)
    {
        $action = '';
        $argument = '';
        switch ($subType)
        {
            case "mobile":
                $action   = 'unsubscribeFromAllByMobile';
                $argument = 'mobile';
                break;
            case "email":
                $action   = 'unsubscribeFromAllByEmail';
                $argument = 'email';
                break;
            case "id":
                $action   = 'unsubscribeFromAllById';
                $argument = 'sid';
                break;
        }
        $this->action = $action;
        $this->argument = $argument;
    }

    public function runUserApiCall($subscriberData)
    {
        $argKey = $this->argument;
        $fields = array(
            'sessionid' => "$this->sessionId",
            'action'    => $this->action,
            "$argKey"   => $subscriberData
        );
        return $this->callApi($fields);
    }

    public function unsubscribeFromCid($sid, $cid)
    {
        $fields = array(
            'sessionid' => "$this->sessionId",
            'action'    => "unsubscribeFromCID",
            'sid'       => $sid,
            'cid'       => $cid
        );
        return $this->callApi($fields);
    }
}
