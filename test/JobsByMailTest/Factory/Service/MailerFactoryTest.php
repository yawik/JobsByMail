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
use JobsByMail\Factory\Service\MailerFactory;
use Core\Mail\MailService;
use Core\Options\ModuleOptions;
use Organizations\ImageFileCache\Manager as OrganizationImageCache;
use Laminas\Log\LoggerInterface as Log;
use JobsByMail\Service\Mailer;
use JobsByMail\Service\Hash;

/**
 * @coversDefaultClass \JobsByMail\Factory\Service\MailerFactory
 */
class MailerFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__invoke
     */
    public function testInvoke()
    {
        $mailService = $this->getMockBuilder(MailService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $coreOptions = $this->getMockBuilder(ModuleOptions::class)
            ->getMock();
        
        $organizationImageCache = $this->getMockBuilder(OrganizationImageCache::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $errorLogger = $this->getMockBuilder(Log::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ContainerInterface::class)
            ->getMock();
        $container->expects($this->exactly(5))
            ->method('get')
            ->will($this->returnValueMap([
                ['Core/MailService', $mailService],
                [Hash::class, new Hash()],
                ['Core/Options', $coreOptions],
                ['Organizations\ImageFileCache\Manager', $organizationImageCache],
                ['Log/Core/Mail', $errorLogger],
            ]));
        
        
        $serviceFactory = new MailerFactory();
        $service = $serviceFactory->__invoke($container, Mailer::class);
        $this->assertInstanceOf(Mailer::class, $service);
    }
    
    /**
     * @covers ::createService
     */
    public function testCreateService()
    {
        $service = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $container = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        
        $serviceFactory = $this->getMockBuilder(MailerFactory::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $serviceFactory->expects($this->once())
            ->method('__invoke')
            ->with($this->identicalTo($container), $this->identicalTo(Mailer::class))
            ->willReturn($service);
        
        $this->assertSame($service, $serviceFactory->createService($container));
    }
}
