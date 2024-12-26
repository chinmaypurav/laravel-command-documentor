<?php

return [
    /**
     * List of command signatures to be excluded.
     */
    'exclude' => [
        'hidden' => true,

        'signatures' => [
            // list of command signatures to be excluded
        ],
        'namespaces' => [
            // list of command namespaces to be excluded
        ],
    ],

    /**
     * List of command signatures to be included. This has precedence over the exclude list.
     * This when present will only include the commands with the specified signatures
     * and then exclude the exclude list.
     */
    'include' => [
        'signatures' => [
            // list of command signatures ONLY to be included
        ],
        'namespaces' => [
            // list of command namespaces ONLY to be included
        ],
    ],

    'output' => [
        'disk' => 'local',
        'path' => 'docs/commands',
        'filename' => env('APP_NAME', 'laravel').'_commands.md',
    ],
];
