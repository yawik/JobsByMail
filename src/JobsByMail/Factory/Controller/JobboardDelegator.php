<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Listener\JobboardControllerListener;

class JobboardDelegator implements DelegatorFactoryInterface
{
    
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param callable $callback
     * @param array $options
     * @return \Jobs\Controller\JobboardController
     */
    public function __invoke(ContainerInterface $container, $requestedName, callable $callback, array $options = null)
    {
        /** @var \Jobs\Controller\JobboardController $controller */
        $controller = $callback();
        (new JobboardControllerListener())->attach($controller->getEventManager());
        
        return $controller;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        return $this($serviceLocator, $requestedName, $callback);
    }
}