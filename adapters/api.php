<?php
/*
 * API adapter
 */
class ApiAdapter
{
    function __construct($url)
    {
        $this->handler = $this->getCurl($url);
    }
    
    private function getCurl($url)
    {
        // Returns curl handler
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        return $ch;
    }

    public function callApi($fields)
    {
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, false);
        return new SimpleXMLElement(curl_exec($this->handler));
    }
}