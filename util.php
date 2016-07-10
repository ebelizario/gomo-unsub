<?php
/*
 * util.php
 */
function checkWasNotPosted()
{
    if (empty($_POST))
    {
        returnHomeError();
    }
}

function returnHomeError()
{
    header("Location: index.php?error=1");
}

function getCidsArray()
{
    // Returns an array of CIDs
    $cids = getPostVar('cid');
    return array_filter(explode(",", $cids));
}

function getPostVar($key)
{
    // Get the subscription type
    if (isset($_POST[$key]))
    {
        return $_POST[$key];
    }
}

