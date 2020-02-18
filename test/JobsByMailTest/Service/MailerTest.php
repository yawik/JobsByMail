<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Service;

use JobsByMail\Service\Mailer;
use Core\Mail\MailService;
use JobsByMail\Service\Hash;
use Core\Options\ModuleOptions;
use Organizations\ImageFileCache\Manager as OrganizationImageCache;
use Laminas\Log\LoggerInterface as Log;
use JobsByMail\Entity\SearchProfile;
use Core\Mail\HTMLTemplateMessage;
use Laminas\I18n\Translator\TranslatorInterface as Translator;
use Laminas\Mail\Exception\RuntimeException as MailRuntimeException;

/**
 * @coversDefaultClass \JobsByMail\Service\Mailer
 */
class MailerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Mailer
     */
    private $mailer;
    
    /**
     * @var MailService
     */
    private $mailService;
    
    /**
     * @var Hash
     */
    private $hash;
    
    /**
     * @var ModuleOptions
     */
    private $moduleOptions;
    
    /**
     * @var OrganizationImageCache
     */
    private $organizationImageCache;
    
    /**
     * @var Log
     */
    private $log;
    
    /**
     * @var HTMLTemplateMessage
     */
    private $mailMessage;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $translator = $this->getMockBuilder(Translator::class)
            ->getMock();
        $translator->method('translate')
            ->willReturnArgument(0);
        
        $this->mailMessage = $this->getMockBuilder(HTMLTemplateMessage::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBodyText'])
            ->getMock();
        $this->mailMessage->setTranslator($translator);
        
        $this->mailService = $this->getMockBuilder(MailService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mailService->method('get')
            ->with('htmltemplate')
            ->willReturn($this->mailMessage);
        
        $this->hash = new Hash();
        $this->moduleOptions = new ModuleOptions();
        
        $this->organizationImageCache = $this->getMockBuilder(OrganizationImageCache::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->log = $this->getMockBuilder(Log::class)
            ->getMock();
        
        $this->mailer = new Mailer(
            $this->mailService,
            $this->hash,
            $this->moduleOptions,
            $this->organizationImageCache,
            $this->log
        );
    }
    
    /**
     * @covers ::__construct()
     * @covers ::sendJobs()
     * @covers ::sendMessage()
     */
    public function testSendJobs()
    {
        $searchProfile = (new SearchProfile())->setEmail('user@domain.tld');
        $jobs = ['job'];
        $defaultUrl = parse_url($this->moduleOptions->getOperator()['homepage']);
        $expectedScheme = $defaultUrl['scheme'];
        $expectedHost = $defaultUrl['host'];
        $expectedPath = isset($defaultUrl['path']) ? $defaultUrl['path'] : null;
        
        $this->assertTrue($this->mailer->sendJobs($searchProfile, $jobs));
        $this->assertContains('New jobs for you', $this->mailMessage->getSubject());
        $this->assertTrue($this->mailMessage->getTo()->has($searchProfile->getEmail()));
        $this->assertSame($searchProfile, $this->mailMessage->getVariable('searchProfile'));
        $this->assertSame($jobs, $this->mailMessage->getVariable('jobs'));
        $this->assertSame($expectedScheme, $this->mailMessage->getVariable('scheme'));
        $this->assertSame($expectedHost, $this->mailMessage->getVariable('host'));
        $this->assertSame($expectedPath, $this->mailMessage->getVariable('basePath'));
        $this->assertSame($this->hash, $this->mailMessage->getVariable('hash'));
        $this->assertSame($this->organizationImageCache, $this->mailMessage->getVariable('organizationImageCache'));
    }
    
    /**
     * @covers ::sendJobs()
     * @covers ::sendMessage()
     * @dataProvider dataSendJobsWithServerUrl
     * @param string $serverUrl
     * @param string $expectedScheme
     * @param string $expectedHost
     * @param string $expectedBasePath
     */
    public function testSendJobsWithServerUrl($serverUrl, $expectedScheme, $expectedHost, $expectedBasePath)
    {
        $searchProfile = new SearchProfile();
        $jobs = ['job'];
        
        $this->assertTrue($this->mailer->sendJobs($searchProfile, $jobs, $serverUrl));
        $this->assertSame($expectedScheme, $this->mailMessage->getVariable('scheme'));
        $this->assertSame($expectedHost, $this->mailMessage->getVariable('host'));
        $this->assertSame($expectedBasePath, $this->mailMessage->getVariable('basePath'));
    }
    /**
     * @covers ::sendJobs()
     * @expectedException \InvalidArgumentException
     * @expectedMessage ServerUrl is invalid
     */
    public function testSendJobsWithInvalidServerUrl()
    {
        $searchProfile = new SearchProfile();
        $jobs = ['job'];
        $serverUrl = 'invalid';
        
        $this->assertTrue($this->mailer->sendJobs($searchProfile, $jobs, $serverUrl));
    }
    
    /**
     * @covers ::sendConfirmation()
     * @covers ::sendMessage()
     */
    public function testSendConfirmation()
    {
        $searchProfile = (new SearchProfile())->setEmail('user@domain.tld');
        
        $this->assertTrue($this->mailer->sendConfirmation($searchProfile));
        $this->assertContains('Confirm your search profile', $this->mailMessage->getSubject());
        $this->assertTrue($this->mailMessage->getTo()->has($searchProfile->getEmail()));
        $this->assertSame($searchProfile, $this->mailMessage->getVariable('searchProfile'));
        $this->assertSame($this->hash, $this->mailMessage->getVariable('hash'));
    }
    
    /**
     * @covers ::sendConfirmation()
     * @expectedException \LogicException
     * @expectedMessage search profile is not a draft
     */
    public function testSendConfirmationWithNonDraftProfile()
    {
        $searchProfile = (new SearchProfile())->setIsDraft(false);
        
        $this->mailer->sendConfirmation($searchProfile);
    }
    
    /**
     * @covers ::sendMessage()
     */
    public function testSendMessageFailed()
    {
        $this->mailService->method('send')
            ->willThrowException(new MailRuntimeException());
        
        $searchProfile = (new SearchProfile())->setEmail('user@domain.tld');
        $jobs = ['job'];
        
        $this->assertFalse($this->mailer->sendJobs($searchProfile, $jobs));
    }
    
    /**
     * @return array
     */
    public function dataSendJobsWithServerUrl()
    {
        return [
            ['http://domain.tld', 'http', 'domain.tld', null],
            ['http://domain.tld/', 'http', 'domain.tld', '/'],
            ['https://domain.tld/base-path', 'https', 'domain.tld', '/base-path']
        ];
    }
}
