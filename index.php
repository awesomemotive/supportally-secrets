<?php
/**
 * Index file. Start it all.
 */
namespace ShareSecret;
require_once __DIR__.'/vendor/autoload.php';

$share_secret = new ShareSecret();
$share_secret->init();

if ( isset( $_POST['secret_url'] ) ) {
	$delete_secret = new DeleteSecret();
	$delete_secret->delete_secret();
} elseif ( isset( $_POST['secret'] ) ) {
	$save_secret = new SaveSecret();
	$save_secret->save_data();
} elseif( isset( $_GET['view'] ) ) {
	$view_secret = new ViewSecret();
	$view_secret->display();
} else {
	$share_secret->display_form();
}