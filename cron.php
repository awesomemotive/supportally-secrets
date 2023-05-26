<?php 
/**
 * Call the delete secrers.
 */
namespace ShareSecret;
require_once __DIR__.'/inc/ShareSecret.php';

$secrets = new ShareSecret();
$secrets->delete_data();