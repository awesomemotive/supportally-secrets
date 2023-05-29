<?php 
/**
 * Call the delete secrers.
 */
namespace ShareSecret;

require_once __DIR__.'/inc/ShareSecretController.php';

$secrets = new ShareSecretController();
$secrets->delete_expired_secrets();