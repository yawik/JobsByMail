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
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Jobs\Entity\Location;

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
        $paginatorService = $container->get('ControllerPluginManager')->get('paginator');
        
        return new \JobsByMail\Service\Subscriber($repository, $paginatorService, new Location());
    }

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Subscriber::class);
    }
}