<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Factory\View\Helper\SubscriptionFormFactory;
use JobsByMail\View\Helper\SubscriptionForm;
use JobsByMail\Form\SubscribeForm;
use Core\Controller\Plugin\PaginationParams;
use Auth\AuthenticationService;

/**
 * @coversDefaultClass \JobsByMail\Factory\View\Helper\SubscriptionFormFactory
 */
class SubscriptionFormFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $form = $this->getMockBuilder(SubscribeForm::class)
            ->getMock();
        
        $formElementManager = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $formElementManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo(SubscribeForm::class))
            ->willReturn($form);
        
        $paginationParams = $this->getMockBuilder(PaginationParams::class)
            ->getMock();
        
        $controllerPluginManager = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $controllerPluginManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo('paginationParams'))
            ->willReturn($paginationParams);
        
        $authenticationService = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap([
                ['FormElementManager', $formElementManager],
                ['ControllerPluginManager', $controllerPluginManager],
                ['AuthenticationService', $authenticationService],
            ]));
        
        
        $serviceFactory = new SubscriptionFormFactory();
        $service = $serviceFactory->__invoke($container, SubscriptionForm::class);
        $this->assertInstanceOf(SubscriptionForm::class, $service);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $service = $this->getMockBuilder(SubscriptionForm::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has'])
            ->getMock();
        $container->expects($this->once())
            ->method('getServiceLocator')
            ->willReturnSelf();
        
        $serviceFactory = $this->getMockBuilder(SubscriptionFormFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $serviceFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(SubscriptionForm::class))
            ->willReturn($service);
        
        $this->assertSame($service, $serviceFactory->createService($container));
    }
}
