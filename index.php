<?php

/**
 * Index file. Start it all.
 */

namespace ShareSecret;

require_once __DIR__ . '/vendor/autoload.php';

$share_secret = new ShareSecretFactory();
$share_secret->create_share_secret()->init();
