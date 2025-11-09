<?php
return [
    // Framework version
    'version' => '0.01',

    // The name of your application (shown in the navbar and title)
    'app_name' => 'Coolscript MVC Framework',

    // The base URL of your site (used for routing and links)
    'base_url' => '/',

    // The default controller loaded for the root URL or fallback
    'default_controller' => 'HomeController',

    // Enable CSRF protection for all forms and requests (true/false)
    'csrf_enabled' => false,

    // Enforce HTTPS for all requests (true/false)
    'force_https' => true,

    // Session handling mode: 'cookie' (default PHP sessions) or 'sql' (store sessions in database)
    // If 'sql', the framework will auto-create a session table in the configured database
    'session_handler' => 'cookie', // Options: 'cookie', 'sql'

    // Debug mode: if true, PHP errors are displayed; if false, errors are hidden
    'debug' => true,

    // Session timeout in seconds (how long a session is valid, default: 1440 = 24 minutes)
    'session_timeout' => 1440,

    // Session garbage collection probability (default: 1 means 1/100 chance per request)
    'session_gc_probability' => 1, // numerator
    'session_gc_divisor' => 100,   // denominator

    // Enable request logging (true/false)
    'logging_enabled' => false,

    // Enable error logging (true/false)
    'error_logging_enabled' => false,
];
