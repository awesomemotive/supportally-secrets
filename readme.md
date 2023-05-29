
### SupportAlly
This application gets a password from a user and generates a secret link, where to view it. The user also has an option to delete it.
The secrets are kept for 30 days or until deleted, whichever comes first.

### Set UP
<ol>
	<li>Run composer install to get the autoloader and coding standards</li>
	<li>Add a `wp-config.php` file and add there constants for the db details, the SODIUM key, and the reCaptcha key</li>
	<li>The key should be in SODIUM format, I generated mine with `sodium_crypto_secretbox_keygen();`
   and `sodium_bin2hex();`</li>
   <li>An images folder is needed for the AM logos, I will upload this one once I have access</li>
   <li>The cron needs to be setup to call cron.php</li>
</ol>