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
            ],
            'annotation' => [
                'paths' => [
                    __DIR__ . '/../src/JobsByMail/Entity'
                ]
            ]
        ]
    ],
    'service_manager' => [
        'factories' => [
            \JobsByMail\Service\Subscriber::class => \JobsByMail\Factory\SubscriberFactory::class
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
            'JobsByMail/SubscribeController' => 'JobsByMail\Factory\Controller\SubscribeControllerFactory',
            'JobsByMail/ConsoleController' => 'JobsByMail\Factory\Controller\ConsoleControllerFactory'
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
    'console' => [
        'router' => [
            'routes' => [
                'jobsbymail-send' => [
                    'options' => [
                        'route' => 'jobsbymail send [--limit=]',
                        'defaults' => [
                            'controller' => 'JobsByMail/ConsoleController',
                            'action' => 'send',
                            'limit' => '30'
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
