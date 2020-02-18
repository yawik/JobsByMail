<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Controller;


use JobsByMail\Controller\ConfirmController;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Service\Hash;
use JobsByMail\Service\Subscriber;

class ConfirmControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return ConfirmController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchProfileRepository = $container->get('repositories')->get('JobsByMail/SearchProfile');
        $subscriber = $container->get(Subscriber::class);
        $hash = $container->get(Hash::class);
        $translator = $container->get('translator');
        
        return new ConfirmController($searchProfileRepository, $subscriber, $hash, $translator);
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ConfirmController::class);
    }
}