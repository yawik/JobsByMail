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
use JobsByMail\Factory\Controller\SubscribeControllerFactory;
use JobsByMail\Controller\SubscribeController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Mailer;
use JobsByMail\Service\Subscriber;
use Zend\View\Renderer\RendererInterface;

/**
 * @coversDefaultClass \JobsByMail\Factory\Controller\SubscribeControllerFactory
 */
class SubscribeControllerFactoryTest extends \PHPUnit_Framework_TestCase
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
        
        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $viewRenderer = $this->getMockBuilder(RendererInterface::class)
            ->getMock();
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(5))
            ->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories],
                [Subscriber::class, $subscriber],
                [Mailer::class, $mailer],
                ['FormElementManager', $container],
                ['ViewRenderer', $viewRenderer]
            ]));
        
        
        $controllerFactory = new SubscribeControllerFactory();
        $controller = $controllerFactory->__invoke($container, SubscribeController::class);
        $this->assertInstanceOf(SubscribeController::class, $controller);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $controller = $this->getMockBuilder(SubscribeController::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has'])
            ->getMock();
        $container->expects($this->once())
            ->method('getServiceLocator')
            ->willReturnSelf();
        
        $controllerFactory = $this->getMockBuilder(SubscribeControllerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $controllerFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(SubscribeController::class))
            ->willReturn($controller);
        
        $this->assertSame($controller, $controllerFactory->createService($container));
    }
}
