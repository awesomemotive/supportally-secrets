<?php 
namespace ShareSecret;

use ShareSecret\Controllers\ShareSecretController;
use ShareSecret\Models\ShareSecretModel;

class ShareSecretFactory {
	public function create_share_secret(): ShareSecretController {
		return new ShareSecretController( new ShareSecretModel() );
	}
}