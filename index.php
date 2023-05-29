<?php
/**
 * Index file. Start it all.
 */
namespace ShareSecret;
use ShareSecret\Controllers\ShareSecretController;

require_once __DIR__.'/vendor/autoload.php';

$share_secret = new ShareSecretController();
$share_secret->init();