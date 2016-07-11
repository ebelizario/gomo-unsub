<?php
/*
 * Logger
 */

class Logger
{
    public $logfile;
    private $_level = array(
        "debug"   => "DEBUG",
        "info"    => "INFO",
        "warning" => "WARN",
        "alert"   => "ALERT",
        "error"   => "ERROR"
    );

    public function __construct($path, $suffix)
    {
        $this->logfile = $path . "/" . date("Y-m-d") . $suffix;
    }

    public function debug($msg, $output=true)
    {
        $this->_log($this->_level['debug'], $msg, $output);
    }

    public function info($msg, $output=true)
    {
        $this->_log($this->_level['info'], $msg, $output);
    }

    public function warning($msg, $output=true)
    {
        $this->_log($this->_level['warning'], $msg, $output);
    }

    public function alert($msg, $output=true)
    {
        $this->_log($this->_level['alert'], $msg, $output);
    }

    public function error($msg, $output=true)
    {
        $this->_log($this->_level['error'], $msg, $output);
    }

    private function _log($level, $msg, $output)
    {
        if ($output)
        {
            echo $msg . "<br />";
        }
        $msg = "[{$level}] {$msg}";
        $handler = fopen($this->logfile, 'a');
        fwrite($handler, $this->_getTimestamp() . " (UTC) : " . $msg . "\r\n");
    }

    private function _getTimestamp()
    {
        return strftime("%m/%d/%Y @ %I:%M:%S%p", time());
    }

    public function printHeader($sessionId, $csvFile, $cids=array())
    {
        $this->info("=== Processing started ===");
        $this->info("SESSIONID: $sessionId");
        $this->info("CSV FILE: " . $csvFile);
        if (count($cids) > 0)
        {
            $this->info("CIDs: " . implode(",", $cids));
        }
    }

    public function printFooter()
    {
        $this->info("=== Processing finished ===");
    }
}
