
### SupportAlly
This application gets a password from a user and generates a secret link, where to view it. The user also has an option to delete it.

The secrets are kept for 30 days or until deleted, whichever comes first.

### Set up
2. Run `composer install --no-dev` for the autoloader and the necessary dependencies.
2. Add a `wp-config.php` file and add there constants for the database details, the sodium key, and the reCaptcha key. The key should be in sodium format.
4. The cron needs to be setup to call `cron.php`

### Generating a Sodium Key

To generate a sodium key, create a temporary PHP file in the `deploy` directory that includes the Composer autoloader and the following lines of code:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
echo sodium_bin2hex(
    sodium_crypto_secretbox_keygen()
);

```

When done, execute the file with PHP and copy the result to the proper location in `wp-config.php`.

**Note:** Whenever changes are committed to GitHub, `.gitignore` is set up so that it only allows the autoloader to be committed; no coding standards or other dependencies will be added.
