<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Service;


use JobsByMail\Service\Subscriber;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class SubscriberFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return Subscriber
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get('repositories')->get('JobsByMail/SearchProfile');
        
        return new Subscriber($repository);
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Subscriber::class);
    }
}