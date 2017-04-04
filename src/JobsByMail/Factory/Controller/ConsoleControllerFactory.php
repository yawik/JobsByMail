<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Controller;


use JobsByMail\Controller\ConsoleController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Console\ProgressBar;

class ConsoleControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return ConsoleController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchProfileRepository = $container->get('repositories')->get('JobsByMail/SearchProfile');
        $subscriber = $container->get(\JobsByMail\Service\Subscriber::class);
        $mailer = $container->get(\JobsByMail\Service\Mailer::class);
        $options = $container->get('JobsByMail/SubscribeOptions');
        $progressBarFactory = function ($count, $persistenceNamespace = null) {
            return new ProgressBar($count, $persistenceNamespace);
        };
        
        return new ConsoleController($searchProfileRepository, $subscriber, $mailer, $options, $progressBarFactory);
    }

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), ConsoleController::class);
    }
}