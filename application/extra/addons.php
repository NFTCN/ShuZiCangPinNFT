<?php

return [
    'autoload' => false,
    'hooks' => [
        'sms_send' => [
            'alisms',
            'smsbao',
        ],
        'sms_notice' => [
            'alisms',
            'smsbao',
        ],
        'sms_check' => [
            'alisms',
            'smsbao',
        ],
        'app_init' => [
            'epay',
        ],
        'config_init' => [
            'third',
        ],
    ],
    'route' => [
        '/third$' => 'third/index/index',
        '/third/connect/[:platform]' => 'third/index/connect',
        '/third/callback/[:platform]' => 'third/index/callback',
        '/third/bind/[:platform]' => 'third/index/bind',
        '/third/unbind/[:platform]' => 'third/index/unbind',
    ],
    'priority' => [],
    'domain' => '',
];
