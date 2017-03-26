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
    'options' => [
        'JobsByMail/SubscribeOptions' => [
            'class' => 'JobsByMail\Options\SubscribeOptions'
        ]
    ],
    'form_elements' => [
        'factories' => [
            'JobsByMail\Form\SubscribeForm' => 'JobsByMail\Factory\Form\SubscribeFactory'
        ]
    ],
    'controllers' => [
        'factories' => [
            'JobsByMail/SubscribeController' => 'JobsByMail\Factory\Controller\SubscribeControllerFactory'
        ],
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
    'router' => [
        'routes' => [
            'lang' => [
                'child_routes' => [
                    'jobsbymail' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/jobsbymail',
                            'defaults' => [
                                'controller' => 'JobsByMail/SubscribeController'
                            ]
                        ],
                        'child_routes' => [
                            'subscribe' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/subscribe',
                                    'defaults' => [
                                        'action' => 'subscribe'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    'view_manager' => [
        'template_map' => [],
        'template_path_stack' => [
            __DIR__ . '/../view'
        ]
    ]
];
