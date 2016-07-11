<?php
/**
 * config.php
 */
define('__APP_ROOT__', dirname(dirname(__FILE__)));
$config = array(
    'url'         => 'http://driscollhp.gomocampaign.com/api.php',
    'username'    => 'driscoll_admin',
    'password'    => 'thyxcAjk518',
    'logpath'     => __APP_ROOT__ . '/unsub_log',
    'logsuffix'   => '_unsubbed.log',
    'loglevel'    => 'DEBUG'
);
