<?php
declare(strict_types=1);

/*
 * Copy this file to config/secrets.php on the server OR set these
 * values as environment variables. Never commit real secret keys.
 */
return [
    'paystack_secret_key' => getenv('ELEVEN8_PAYSTACK_SECRET_KEY') ?: '',
];
