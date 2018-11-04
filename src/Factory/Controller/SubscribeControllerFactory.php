<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Controller;


use JobsByMail\Controller\SubscribeController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Mailer;

class SubscribeControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return SubscribeController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchProfileRepository = $container->get('repositories')->get('JobsByMail/SearchProfile');
        $subscriber = $container->get(Subscriber::class);
        $mailer = $container->get(Mailer::class);
        $formElementManager = $container->get('FormElementManager');
        $viewRenderer = $container->get('ViewRenderer');
        
        return new SubscribeController($searchProfileRepository, $subscriber, $mailer, $formElementManager, $viewRenderer);
    }

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), SubscribeController::class);
    }
}