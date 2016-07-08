<?php
/**
 * unsubscribe.php
 *
 * Unsubscribes users by mobile, email, or sid
 * Optionally, cid(s) can be provided
 *
 */
define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__ . '/config/config.php');
require_once(__ROOT__ . '/handlers/gomo.php');
require_once(__ROOT__ . '/handlers/csv.php');
require_once(__ROOT__ . '/handlers/logger.php');
require_once(__ROOT__ . '/util.php');

$logger = new Logger($config['logpath'], $config['logsuffix']);
main($config, $logger);

function main($config, $logger)
{
    checkWasNotPosted();
    $cids = getCidsArray();
    $subType = getPostVar('subtype');

    // Initiate handlers
    $csv = new FileHandler();
    $gomoApi = new GomoHandler($config['url'], $subType);
    $sessionId = $gomoApi->getSessionId($config['username'], $config['password']);

    // Upload file
    $csv->uploadFile($_FILES);
    $csvFile = @fopen($csv->getUploadedFilePath(), "r");
    if (!$csvFile)
    {
        returnHomeError();
    }

    $logger->log("=== Processing started ===");
    $logger->log("SESSIONID: $sessionId");
    $logger->log("CSV FILE: " . $csv->getUserFilename());
    $logger->log("CIDs (?): " . implode(",", $cids));

    $subscriberData = $csv->getUserDataArrayFromFile();
    foreach ($subscriberData as $subData)
    {
        if (count($cids) > 0)
        {
            // Unsubscribe by CID(s)
            if ($subType == 'mobile')
            {
                // Get all known SIDS for this subscriber
                $knownUserIds = $gomoApi->getAllKnownIdsByMobile($sessionId, $subData);
            }
            else if ($subType == 'id')
            {
                // Get all known SIDS for this subscriber
                $knownUserIds = array($subData);
            }
            $fields = array(
                'sessionid' => "$sessionId",
                'action'    => "unsubscribeFromCID"
            );
            foreach ($cids as $cid){
                foreach ($knownUserIds as $sid){
                    $fields['sid'] = $sid;
                    $fields['cid'] = $cid;
                    $output = $gomoApi->callApi($fields);
                    $status = $output->status;
                    $msg = "Unsubscribing $subData ($sid) from $cid";
                    $logger->log($msg);
                }
            }
        }
        else
        {
            $argKey = $gomoApi->argument;
            // Otherwise, go the traditional route
            $fields = array(
                'sessionid' => $sessionId,
                'action'    => $gomoApi->action,
                "$argKey"   => $subData
            );
            $output = $gomoApi->callApi($fields);
            $total = $output->affected_rows;
            $status = $output->status;
            $msg = "Unsubscribing $subData: " . $total . " row affected";
            $logger->log($msg);
        }
    }
    $logger->log("=== Processing finished ===");
}