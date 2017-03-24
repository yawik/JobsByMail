<?php

/**
 * create a config/autoload/JobsByMail.local.php and put modifications there.
 */
return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'JobsByMail\Entity' => 'annotation'
                ]
            ]
        ]
    ],
    'form_elements' => [
        'invokables' => [
            'JobsByMail\Form\SubscribeForm' => 'JobsByMail\Form\SubscribeForm',
        ],
    ],
    'controllers' => [
        'delegators' => [
            'Jobs/Jobboard' => [
                'JobsByMail\Factory\Controller\JobboardDelegator'
            ]
        ]
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo'
            ]
        ]
    ],
    'router' => [],
    'view_manager' => [
        'template_map' => [],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ]
    ]
];
