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
use Zend\Log\LoggerInterface as Log;
use JobsByMail\Entity\SearchProfile;
use Core\Mail\HTMLTemplateMessage;
use Zend\I18n\Translator\TranslatorInterface as Translator;
use Zend\Mail\Exception\RuntimeException as MailRuntimeException;

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
        
        $this->organizationImageCache = $this->getMockBuilder(OrganizationImageCache::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->log = $this->getMockBuilder(Log::class)
            ->getMock();
        
        $this->mailer = new Mailer(
            $this->mailService,
            $this->hash,
            new ModuleOptions(),
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
        
        $this->assertTrue($this->mailer->sendJobs($searchProfile, $jobs));
        $this->assertContains('New jobs for you', $this->mailMessage->getSubject());
        $this->assertTrue($this->mailMessage->getTo()->has($searchProfile->getEmail()));
        $this->assertSame($searchProfile, $this->mailMessage->getVariable('searchProfile'));
        $this->assertSame($jobs, $this->mailMessage->getVariable('jobs'));
        $this->assertSame($this->hash, $this->mailMessage->getVariable('hash'));
        $this->assertSame($this->organizationImageCache, $this->mailMessage->getVariable('organizationImageCache'));
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
}
