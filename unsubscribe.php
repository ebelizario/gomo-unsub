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

// Create logger
$logger = new Logger($config['logpath'], $config['logsuffix']);

// Unsubscribe engine
main($config, $logger);

function main($config, $logger)
{
    checkWasNotPosted();
    $cids = getCidsArray();
    $subType = getPostVar('subtype');

    // TODO: Add validation

    // Initiate handlers
    $csv = new FileHandler();
    $gomoApi = new GomoHandler($config['url'], $config['username'], $config['password'], $subType);

    // Upload file
    $csv->uploadFile($_FILES);
    $csvFile = @fopen($csv->getUploadedFilePath(), "r");
    if (!$csvFile)
    {
        returnHomeError();
    }

    $logger->printHeader(
        $gomoApi->getSessionId(),
        $csv->getUserFilename(),
        $cids
    );

    $subscriberData = $csv->getUserDataArrayFromFile();
    foreach ($subscriberData as $subData)
    {
        if (count($cids) > 0)
        {
            // Unsubscribe by CID(s)
            if ($subType == 'mobile')
            {
                // Get all known SIDS for this subscriber
                $knownUserIds = $gomoApi->getAllKnownIdsByMobile($subData);
            }
            else if ($subType == 'id')
            {
                // Get all known SIDS for this subscriber
                $knownUserIds = array($subData);
            }
            foreach ($cids as $cid){
                foreach ($knownUserIds as $sid){
                    $output = $gomoApi->unsubscribeFromCid($sid, $cid);
                    $status = $output->status;
                    $message = $output->message;
                    $msg = "Unsubscribing $subData ($sid) from $cid ... [$status:$message]";
                    $logger->log($msg);
                }
            }
        }
        else
        {
            // Otherwise, go the traditional route
            $output = $gomoApi->runUserApiCall($subData);
            $total  = $output->affected_rows;
            $status = $output->status;
            $message = $output->message;
            $msg    = "Unsubscribing $subData ... [$status:$message]: " . $total . " row affected";
            $logger->log($msg);
        }
    }
    $logger->printFooter();
}
