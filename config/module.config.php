<?php
use JobsByMail\Service;
use JobsByMail\Factory\Service as ServiceFactory;
use JobsByMail\Factory\Controller as ControllerFactory;

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
                    __DIR__ . '/../src/Entity'
                ]
            ]
        ]
    ],
    'service_manager' => [
        'invokables' => [
            Service\Hash::class => Service\Hash::class
        ],
        'factories' => [
            Service\Subscriber::class => ServiceFactory\SubscriberFactory::class,
            Service\Mailer::class => ServiceFactory\MailerFactory::class,
            Service\JobSeeker::class => ServiceFactory\JobSeekerFactory::class
        ]
    ],
    'options' => [
        'JobsByMail/SubscribeOptions' => [
            'class' => \JobsByMail\Options\SubscribeOptions::class
        ]
    ],
    'form_elements' => [
        'factories' => [
            'JobsByMail\Form\SubscribeForm' => \JobsByMail\Factory\Form\SubscribeFactory::class
        ]
    ],
    'controllers' => [
        'factories' => [
            'JobsByMail/SubscribeController' => ControllerFactory\SubscribeControllerFactory::class,
            'JobsByMail/UnsubscribeController' => ControllerFactory\UnsubscribeControllerFactory::class,
            'JobsByMail/ConfirmController' => ControllerFactory\ConfirmControllerFactory::class,
            'JobsByMail/ConsoleController' => ControllerFactory\ConsoleControllerFactory::class
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
                                'action' => 'index'
                            ]
                        ],
                        'child_routes' => [
                            'subscribe' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/subscribe',
                                    'defaults' => [
                                        'controller' => 'JobsByMail/SubscribeController',
                                    ]
                                ]
                            ],
                            'unsubscribe' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/unsubscribe/:id/:hash',
                                    'defaults' => [
                                        'controller' => 'JobsByMail/UnsubscribeController'
                                    ],
                                    'constraints' => [
                                        'id' => '[a-z0-9]{24}',
                                        'hash' => '[a-z0-9]{32}'
                                    ]
                                ]
                            ],
                            'confirm' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/confirm/:id/:hash',
                                    'defaults' => [
                                        'controller' => 'JobsByMail/ConfirmController'
                                    ],
                                    'constraints' => [
                                        'id' => '[a-z0-9]{24}',
                                        'hash' => '[a-z0-9]{32}'
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
                        'route' => 'jobsbymail send [--limit=] [--server-url=]',
                        'defaults' => [
                            'controller' => 'JobsByMail/ConsoleController',
                            'action' => 'send',
                            'limit' => '30'
                        ]
                    ]
                ],
                'jobsbymail-cleanup' => [
                    'options' => [
                        'route' => 'jobsbymail cleanup',
                        'defaults' => [
                            'controller' => 'JobsByMail/ConsoleController',
                            'action' => 'cleanup'
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
    ],
    'view_helpers' => [
        'factories' => [
            'jobsByMailSubscriptionForm' => \JobsByMail\Factory\View\Helper\SubscriptionFormFactory::class
        ]
    ]
];
