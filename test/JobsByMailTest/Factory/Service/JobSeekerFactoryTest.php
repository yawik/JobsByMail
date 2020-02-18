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
use JobsByMail\Factory\Service\JobSeekerFactory;
use JobsByMail\Service\JobSeeker;

/**
 * @coversDefaultClass \JobsByMail\Factory\Service\JobSeekerFactory
 */
class JobSeekerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $paginator = function () {};
        
        $controllerPluginManager = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $controllerPluginManager->expects($this->once())
            ->method('get')
            ->with($this->equalTo('paginator'))
            ->willReturn($paginator);
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(1))
            ->method('get')
            ->will($this->returnValueMap([
                ['ControllerPluginManager', $controllerPluginManager]
            ]));
        
        
        $serviceFactory = new JobSeekerFactory();
        $service = $serviceFactory->__invoke($container, JobSeeker::class);
        $this->assertInstanceOf(JobSeeker::class, $service);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $service = $this->getMockBuilder(JobSeeker::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        
        $serviceFactory = $this->getMockBuilder(JobSeekerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $serviceFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(JobSeeker::class))
            ->willReturn($service);
        
        $this->assertSame($service, $serviceFactory->createService($container));
    }
}
