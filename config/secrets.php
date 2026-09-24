<?php
declare(strict_types=1);

/* Local/server-only secrets. This file is ignored by Git. */
return [
    'paystack_secret_key' => getenv('ELEVEN8_PAYSTACK_SECRET_KEY') ?: '',
];
