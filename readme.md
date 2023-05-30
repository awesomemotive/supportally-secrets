
### SupportAlly
This application gets a password from a user and generates a secret link, where to view it. The user also has an option to delete it.

The secrets are kept for 30 days or until deleted, whichever comes first.

### Set up
1. Run `composer dump-autoload` to get the autoloader
2. Run `composer install` to get the autoloading _and_ coding standards
2. Add a `wp-config.php` file and add there constants for the database details, the SODIUM key, and the reCaptcha key
3. The key should be in SODIUM format, I generated mine with `sodium_crypto_secretbox_keygen();` and `sodium_bin2hex();`
4. The cron needs to be setup to call `cron.php`

**Note:** Whenever changes are committed to GitHub, `.gitignore` is set up so that it only allows the autoloader to be committed; no coding standards or other dependencies will be added.
