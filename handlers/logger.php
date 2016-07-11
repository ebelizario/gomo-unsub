<?php
/*
 * Logger
 */

class Logger
{
    public $logfile;
    private $_level = array(
        10 => "DEBUG",
        20 => "INFO",
        30 => "WARNING",
        40 => "ALERT",
        50 => "ERROR"
    );

    public function __construct($path, $suffix, $logLevel)
    {
        $this->logLevel = $logLevel;
        $this->logfile  = $path . "/" . date("Y-m-d") . $suffix;
    }

    public function __call($name, $arguments)
    {
		if (method_exists($this, $name))
        {
			// Run method within class
            $this->$name($arguments);
        }
        else
        {
			// Check if method is one of the log levels
			$level = $this->_getLevelLabel($name);
            if (!empty($level))
			{
				// Log according to level
				$msg 	= $arguments[0];
				$output = isset($arguments[1]) ? $arguments[1] : false;
        		$this->_log($level, $msg, $output);
			}
			else
			{
				$this->_log("ERROR", "Unknown method called", true);
			}
        }
    }

    private function _getLevelValue($level)
    {
		$result = "";
        foreach ($this->_level as $key => $value)
        {
            if ($value == strtoupper($level))
            {
                $result = $key;
            	break;
			}
        }
		return $result;
    }

    private function _getLevelLabel($level)
    {
		$result = "";
        foreach ($this->_level as $key => $value)
        {
            if ($value == strtoupper($level))
            {
                $result = $value;
				break;
            }
        }
		return $result;
    }

    private function _isWithinLogLevel($level)
    {
        // Check if the log level is within the config level
        if ($this->_getLevelValue($level) >= $this->_getLevelValue($this->logLevel))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function _log($level, $msg, $output)
    {
        if ($output)
        {
            echo "{$msg}<br />";
        }

        if ($this->_isWithinLogLevel($level))
        {
            $timestamp = $this->_getTimestamp();
            $msg = "{$timestamp} {$level} : {$msg}\r\n";
            $handler = fopen($this->logfile, 'a');
            fwrite($handler, $msg);
        }
    }

    private function _getTimestamp()
    {
        return strftime("%m/%d/%Y %I:%M:%S%p", time());
    }

    public function printHeader($sessionId, $csvFile, $cids=array(),
        $output=false)
    {
        $this->info("=== Processing started ===", $output);
        $this->debug("SESSIONID: {$sessionId}", $output);
        $this->debug("CSV FILE: {$csvFile}", $output);
        if (count($cids) > 0)
        {
            $this->debug("CIDs: " . implode(",", $cids), $output);
        }
    }

    public function printFooter($output=false)
    {
        $this->info("=== Processing finished ===", $output);
    }
}
