<?php

declare(strict_types=1);

return [
    'base_url'   => env('KISTN_BASE_URL', ''),
    'project_id' => env('KISTN_PROJECT_ID', ''),
    'token'      => env('KISTN_TOKEN', ''),
    'cache_path' => storage_path('app/private/kistn/.inventory.hash'),
    'work_dir'   => base_path(),

    // Whether lock/manifest files are uploaded to the Kistn server alongside the
    // inventory. Allowed values: 'true' (always), 'false' (never), 'on-demand'
    // (only when the package manager CLI is unavailable to read them server-side).
    // Any unrecognized value falls back to 'false' (never upload) — fail-safe.
    'transmit_composer_files' => env('KISTN_TRANSMIT_COMPOSER_FILES', 'true'),
    'transmit_npm_files'      => env('KISTN_TRANSMIT_NPM_FILES', 'true'),
];
