<?php
/*
 * Gomo Campaign Handler
 */
define('__HANDLER_ROOT__', dirname(dirname(__FILE__)));
require_once(__HANDLER_ROOT__.'/adapters/api.php');

class GomoHandler extends ApiAdapter
{
    public function __construct($url, $subType)
    {
        $this->getAction($subType);
        parent::__construct($url);
    }

    public function getSessionId($username, $password)
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

    public function getAllKnownIdsByMobile($sessionid, $mobile)
    {
        // Returns an array of known IDs by mobile
        $fields = array(
            'sessionid' => "$sessionid",
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
}