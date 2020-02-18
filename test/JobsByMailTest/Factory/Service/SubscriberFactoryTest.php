<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Factory\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Factory\Service\SubscriberFactory;
use JobsByMail\Service\Subscriber;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;

/**
 * @coversDefaultClass \JobsByMail\Factory\Service\SubscriberFactory
 */
class SubscriberFactoryTest extends \PHPUnit_Framework_TestCase
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
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories],
            ]));
        
        
        $serviceFactory = new SubscriberFactory();
        $service = $serviceFactory->__invoke($container, Subscriber::class);
        $this->assertInstanceOf(Subscriber::class, $service);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $service = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        
        $serviceFactory = $this->getMockBuilder(SubscriberFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $serviceFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(Subscriber::class))
            ->willReturn($service);
        
        $this->assertSame($service, $serviceFactory->createService($container));
    }
}
