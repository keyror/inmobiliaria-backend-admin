<?php

return [
    'enabled' => env('RATE_LIMITS_ENABLED', true),

    'public_properties_per_minute' => env('RATE_LIMIT_PUBLIC_PROPERTIES_PER_MINUTE', 120),
    'public_property_show_per_minute' => env('RATE_LIMIT_PUBLIC_PROPERTY_SHOW_PER_MINUTE', 120),
    'public_property_contact_per_minute' => env('RATE_LIMIT_PUBLIC_PROPERTY_CONTACT_PER_MINUTE', 10),
    'lookups_per_minute' => env('RATE_LIMIT_LOOKUPS_PER_MINUTE', 60),
    'login_per_minute' => env('RATE_LIMIT_LOGIN_PER_MINUTE', 5),
    'password_reset_per_minute' => env('RATE_LIMIT_PASSWORD_RESET_PER_MINUTE', 3),
    'authenticated_api_per_minute' => env('RATE_LIMIT_AUTHENTICATED_API_PER_MINUTE', 300),
    'image_uploads_per_minute' => env('RATE_LIMIT_IMAGE_UPLOADS_PER_MINUTE', 30),
];
