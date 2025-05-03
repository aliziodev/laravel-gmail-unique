<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gmail Domains
    |--------------------------------------------------------------------------
    |
    | List of Gmail domains that will be normalized by this package.
    | Emails with these domains will be processed to remove dots and aliases.
    |
    */
    'domains' => [
        'gmail.com',
        'googlemail.com'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Email Column
    |--------------------------------------------------------------------------
    |
    | The default email column name that will be used for validation.
    | The package will look for this column in the model to perform normalization.
    |
    */
    'email_column' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Error Message
    |--------------------------------------------------------------------------
    |
    | The error message that will be displayed when a normalized email
    | already exists in the database.
    |
    */
    'error_message' => 'This email is already taken (normalized Gmail detected).'
];
