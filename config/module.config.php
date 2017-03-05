<?php

/**
 * create a config/autoload/JobsByMail.local.php and put modifications there.
 */

return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'JobsByMail\Entity' => 'annotation',
                ],
            ],
        ],
    ],
    'form_elements' => [],
    'controllers' => [],
    'translator'   => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'router' => [],
];
