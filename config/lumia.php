<?php

return [
    'privacy_policy_version' => env('LUMIA_PRIVACY_POLICY_VERSION', '2026-1'),
    'prompt_version' => env('LUMIA_PROMPT_VERSION', 'v1'),
    'max_upload_kilobytes' => (int) env('LUMIA_MAX_UPLOAD_KB', 5120),
    'retention_days' => (int) env('LUMIA_RETENTION_DAYS', 180),
    'status_poll_seconds' => 3,
    'filesystem_disk' => env('LUMIA_FILESYSTEM_DISK', 'local'),
];
