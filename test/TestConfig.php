<?php
$modules = [
    'Core',
    'Auth',
    'Geo',
    'Jobs',
    'JobsByMail'
];

// check if Solr is installed
if (is_dir(__DIR__ . '/../../Solr')) {
    $modules[] = 'Solr';
}

return [
    'modules' => $modules,
    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor'
        ],
        
        'config_glob_paths' => [
            'config/autoload/{,*.}{global,local}.php'
        ]
    ]
];
