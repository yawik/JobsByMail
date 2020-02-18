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
use Laminas\ServiceManager\ServiceLocatorInterface;
use JobsByMail\Factory\Controller\ConsoleControllerFactory;
use JobsByMail\Controller\ConsoleController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Service\JobSeeker;
use JobsByMail\Service\Mailer;
use JobsByMail\Options\SubscribeOptions;
use Core\Console\ProgressBar;

/**
 * @coversDefaultClass \JobsByMail\Factory\Controller\ConsoleControllerFactory
 */
class ConsoleControllerFactoryTest extends \PHPUnit_Framework_TestCase
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
        
        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $jobSeeker = $this->getMockBuilder(JobSeeker::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $options = $this->getMockBuilder(SubscribeOptions::class)
            ->getMock();
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(4))
            ->method('get')
            ->will($this->returnValueMap([
                ['repositories', $repositories],
                [JobSeeker::class, $jobSeeker],
                [Mailer::class, $mailer],
                ['JobsByMail/SubscribeOptions', $options]
            ]));
        
        
        $controllerFactory = new ConsoleControllerFactory();
        $controller = $controllerFactory->__invoke($container, ConsoleController::class);
        $this->assertInstanceOf(ConsoleController::class, $controller);
        $this->assertInstanceOf(ProgressBar::class, $controller->getProgressBarFactory()->__invoke(0, 'preventOutput'));
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $controller = $this->getMockBuilder(ConsoleController::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->setMethods(['getServiceLocator', 'get', 'has', 'build'])
            ->getMock();
        $container->expects($this->once())
            ->method('getServiceLocator')
            ->willReturnSelf();
        
        $controllerFactory = $this->getMockBuilder(ConsoleControllerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $controllerFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(ConsoleController::class))
            ->willReturn($controller);
        
        $this->assertSame($controller, $controllerFactory->createService($container));
    }
}
