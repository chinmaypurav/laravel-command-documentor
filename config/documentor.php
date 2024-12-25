<?php

return [
    'exclude' => [
        // list of command signatures to be excluded
    ],

    'output' => [
        'disk' => 'local',
        'path' => 'docs/commands',
        'filename' => env('APP_NAME', 'laravel').'_commands.md',
    ],
];