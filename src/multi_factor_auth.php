<?php

return [
    /*
     * Settings column. By default this column belongs to the users table.
     */
    'column' => 'mfa_enabled',

    /**
     * Name of session identifier.
     */
    'session_name' => 'mfa_verified',

    /*
     * Name of cookie identifier.
     */
    'cookie_name' => 'mfa_identity',

    /*
     * Number of minutes to keep the verified identity active.
     */
    'cookie_lifetime' => 43200, // 30 days
];