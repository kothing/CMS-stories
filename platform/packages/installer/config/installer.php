<?php

return [
    'enabled' => env('CMS_ENABLE_INSTALLER', true),
    'core' => [
        'php_version' => '8.0.2',
    ],
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'JSON',
            'cURL',
            'gd',
            'fileinfo',
            'exif',
            'xml',
            'ctype',
        ],
        'apache' => [
            'mod_rewrite',
        ],
        'permissions' => [
            '.env',
            'storage/framework/',
            'storage/logs/',
            'bootstrap/cache/',
        ],
    ],
];
