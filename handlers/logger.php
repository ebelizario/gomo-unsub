<?php
/*
 * Logger
 */

class Logger
{
    public $logfile;

    public function __construct($path, $suffix)
    {
        $this->logfile = $path . "/" . date("Y-m-d") . $suffix;
    }

    public function log($msg, $output=true)
    {
        if ($output)
        {
            echo $msg . "<br />";
        }
        $handler = fopen($this->logfile, 'a');
        fwrite($handler, $this->getTimestamp() . " (UTC) : " . $msg . "\r\n");
    }

    private function getTimestamp()
    {
        return strftime("%m/%d/%Y @ %I:%M:%S%p", time());
    }
}