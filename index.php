<?php
/**
 * Index file. Start it all.
 */
namespace ShareSecret;

require_once __DIR__.'/vendor/autoload.php';

$share_secret = new ShareSecretController();

if ( isset( $_POST['secret_url'] ) ) {
	$share_secret->delete_secret();
} elseif ( isset( $_POST['secret'] ) ) {
	$share_secret->save_secret();
} elseif( isset( $_GET['view'] ) ) {
	$share_secret->view_secret();
} else {
	$share_secret->display_form();
}