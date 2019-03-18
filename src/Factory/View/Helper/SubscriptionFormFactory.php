<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Factory\View\Helper;


use JobsByMail\View\Helper\SubscriptionForm;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Form\SubscribeForm;

class SubscriptionFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return \JobsByMail\View\Helper\SubscriptionForm
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $form = $container->get('FormElementManager')->get(SubscribeForm::class);
        $paginationParams = $container->get('ControllerPluginManager')->get('paginationParams');
        $authenticationService = $container->get('AuthenticationService');
        
        return new SubscriptionForm($form, $paginationParams, $authenticationService);
    }

    /**
     * {@inheritDoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), SubscriptionForm::class);
    }
}