<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    @author Carsten Bleek <bleek@cross-solution.de>
 */

namespace JobsByMail;

use Core\ModuleManager\ModuleConfigLoader;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Bootstrap module
 */
class Module implements DependencyIndicatorInterface, ConsoleUsageProviderInterface
{

    /**
     * Loads module specific configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return ModuleConfigLoader::load(__DIR__ . '/config');
    }

    /**
     * Loads module specific autoloader configuration.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {

        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    __NAMESPACE__ . 'Test' => __DIR__ . '/test/' . __NAMESPACE__ . 'Test'
                ],
            ],
        ];
    }

    public function getModuleDependencies()
    {
        return ['Jobs'];
    }
    
    public function getConsoleUsage(Console $console)
    {
        return [
            'Send jobs by mail emails',
            'jobsbymail send [--limit]'  => 'Send jobs by mail emails',
            ['--limit=INT', 'Number of search profile to check per run. Default 30. 0 means no limit']
        ];
    }
}
