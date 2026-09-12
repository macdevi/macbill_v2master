<?php
return [
    'timeout' => (int) env('MIKROTIK_TIMEOUT', 5),
    'isolation_profile' => env('MIKROTIK_ISOLATION_PROFILE', 'isolate'),
];