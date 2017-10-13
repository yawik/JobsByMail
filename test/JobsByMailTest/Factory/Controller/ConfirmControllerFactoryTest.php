<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Factory\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Factory\Controller\ConfirmControllerFactory;
use JobsByMail\Controller\ConfirmController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Hash;
use JobsByMail\Service\Subscriber;
use Zend\I18n\Translator\TranslatorInterface as Translator;

/**
 * @coversDefaultClass \JobsByMail\Factory\Controller\ConfirmControllerFactory
 */
class ConfirmControllerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $searchProfileRepository = $this->getMockBuilder(SearchProfileRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $repositories = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $repositories->expects($this->once())
            ->method('get')
            ->with($this->equalTo('JobsByMail/SearchProfile'))
            ->willReturn($searchProfileRepository);
        
        $subscriber = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(4))
            ->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories],
                [Subscriber::class, $subscriber],
                [Hash::class, new Hash()],
                ['Translator', $translator]
            ]));
        
        
        $controllerFactory = new ConfirmControllerFactory();
        $controller = $controllerFactory->__invoke($container, ConfirmController::class);
        $this->assertInstanceOf(ConfirmController::class, $controller);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $controller = $this->getMockBuilder(ConfirmController::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has', 'build'])
            ->getMock();
        $container->expects($this->once())
            ->method('getServiceLocator')
            ->willReturnSelf();
        
        $controllerFactory = $this->getMockBuilder(ConfirmControllerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $controllerFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(ConfirmController::class))
            ->willReturn($controller);
        
        $this->assertSame($controller, $controllerFactory->createService($container));
    }
}
