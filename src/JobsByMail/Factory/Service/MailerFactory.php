<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\Service;


use JobsByMail\Service\Mailer;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MailerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return Mailer
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mailService = $container->get('Core/MailService');
        $moduleOptions = $container->get('Core/Options');
        $organizationImageCache = $container->get('Organizations\ImageFileCache\Manager');
        
        return new \JobsByMail\Service\Mailer($mailService, $moduleOptions, $organizationImageCache);
    }

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Mailer::class);
    }
}