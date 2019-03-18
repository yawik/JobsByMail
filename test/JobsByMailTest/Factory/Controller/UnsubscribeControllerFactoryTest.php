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
use JobsByMail\Factory\Controller\UnsubscribeControllerFactory;
use JobsByMail\Controller\UnsubscribeController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\Hash;
use JobsByMail\Service\Subscriber;

/**
 * @coversDefaultClass \JobsByMail\Factory\Controller\UnsubscribeControllerFactory
 */
class UnsubscribeControllerFactoryTest extends \PHPUnit_Framework_TestCase
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
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories],
                [Subscriber::class, $subscriber],
                [Hash::class, new Hash()],
            ]));
        
        
        $controllerFactory = new UnsubscribeControllerFactory();
        $controller = $controllerFactory->__invoke($container, UnsubscribeController::class);
        $this->assertInstanceOf(UnsubscribeController::class, $controller);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $controller = $this->getMockBuilder(UnsubscribeController::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has', 'build'])
            ->getMock();
        $container->expects($this->once())
            ->method('getServiceLocator')
            ->willReturnSelf();
        
        $controllerFactory = $this->getMockBuilder(UnsubscribeControllerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $controllerFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(UnsubscribeController::class))
            ->willReturn($controller);
        
        $this->assertSame($controller, $controllerFactory->createService($container));
    }
}
