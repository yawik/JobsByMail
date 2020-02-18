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
use JobsByMail\Service\Hash;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $hash = $container->get(Hash::class);
        $moduleOptions = $container->get('Core/Options');
        $organizationImageCache = $container->get('Organizations\ImageFileCache\Manager');
        $log = $container->get('Log/Core/Mail');
        
        return new Mailer($mailService, $hash, $moduleOptions, $organizationImageCache, $log);
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Mailer::class);
    }
}