<?php
$modules = [
    'Zend\ServiceManager\Di',
	'Zend\Session',
	'Zend\Router',
	'Zend\Navigation',
	'Zend\I18n',
	'Zend\Filter',
	'Zend\InputFilter',
	'Zend\Form',
	'Zend\Validator',
	'Zend\Log',
	'Zend\Mvc\Plugin\Prg',
	'Zend\Mvc\Plugin\Identity',
	'Zend\Mvc\Plugin\FlashMessenger',
	'Zend\Mvc\I18n',
	'Zend\Mvc\Console',
	'Zend\Hydrator',
	'Zend\Serializer',
	'DoctrineModule',
	'DoctrineMongoODMModule',
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
