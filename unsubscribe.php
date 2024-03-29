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


/**
 * Start unsubscribe engine
 */
$logger = new Logger(
    $config['logpath'],
    $config['logsuffix'],
    $config['loglevel']
);
main($config, $logger);


/**
 * Main function
 */
function main($config, $logger)
{
    checkWasNotPosted();
    $cids = getCidsArray();
    $subType = getPostVar('subtype');

    // TODO: Add validation

    // Initiate handlers
    $csv = new FileHandler();
    $gomoApi = new GomoHandler(
        $config['url'],
        $config['username'],
        $config['password'],
        $subType
   );

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
        $cids,
        $output=true
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
                if (count($knownUserIds) == 0)
                {
                    $logger->warning("Subscriber not found for {$subData}. ".
                                     " Skipping.", $output=true);
                    continue;
                }

            }
            else if ($subType == 'id')
            {
                // Get all known SIDS for this subscriber
                $knownUserIds = array($subData);
            }
            foreach ($cids as $cid){
                foreach ($knownUserIds as $sid){
                    $logger->info("Unsubscribing {$subData} ({$sid})" .
                                  " from {$cid}", $output=true);
                    $output  = $gomoApi->unsubscribeFromCid($sid, $cid);
                    if ($output->statuscode > 0)
                    {
                        $msg = "{$output->message}";
                        $logger->error($msg);
                    }
                }
            }
        }
        else
        {
            // Otherwise, go the traditional route
            $logger->info("Unsubscribing {$subData}", $output=true);
            $output  = $gomoApi->runUserApiCall($subData);
            $total   = $output->affected_rows;
            $logger->debug("{$total} rows affected");
            if ($output->statuscode > 0)
            {
                $msg = "{$output->message}";
                $logger->error($msg);
            }
        }
    }
    $logger->printFooter($output=true);
}
