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
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\Console\Adapter\AdapterInterface as Console;
use Laminas\ModuleManager\Feature\ConsoleUsageProviderInterface;

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
        return ModuleConfigLoader::load(__DIR__ . '/../config');
    }

    /**
     * {@inheritDoc}
     * @see DependencyIndicatorInterface::getModuleDependencies()
     */
    public function getModuleDependencies()
    {
        return ['Jobs'];
    }
    
    /**
     * {@inheritDoc}
     * @see ConsoleUsageProviderInterface::getConsoleUsage()
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'Send jobs by mail emails',
            'jobsbymail send [--limit] [--server-url]'  => 'Sends emails with relevant jobs to search profiles',
            'jobsbymail cleanup'  => 'Purges stale inactive search profiles',
            ['--limit=INT', 'Number of search profile to check per run. Default 30. 0 means no limit'],
            ['--server-url=STRING', 'Server url including scheme and base path. Examples: http://domain.tld, https://domain.tld/base-path']
        ];
    }
}
