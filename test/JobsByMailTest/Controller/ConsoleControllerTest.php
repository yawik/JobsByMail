<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Controller;

use JobsByMail\Controller\ConsoleController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Options\SubscribeOptions;
use JobsByMail\Service\JobSeeker;
use JobsByMail\Service\Mailer;
use JobsByMail\Entity\SearchProfile;
use Doctrine\Common\Persistence\ObjectManager;
use Core\Console\ProgressBar;
use Doctrine\ODM\MongoDB\Cursor;
use stdClass;
use DateTime;

/**
 * @coversDefaultClass \JobsByMail\Controller\ConsoleController
 */
class ConsoleControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ConsoleController
     */
    private $controller;
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var SubscribeOptions
     */
    private $subscribeOptions;
    
    /**
     * @var JobSeeker
     */
    private $jobSeeker;
    
    /**
     * @var Mailer
     */
    private $mailer;
    
    /**
     * @var callable
     */
    protected $progressBarFactory;
    
    /**
     * @var ProgressBar
     */
    protected $progressBar;
    
    /**
     * @var ObjectManager
     */
    protected $objectManager;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->objectManager = $this->getMockBuilder(ObjectManager::class)
            ->getMock();
        
        $this->searchProfileRepository = $this->getMockBuilder(SearchProfileRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->jobSeeker = $this->getMockBuilder(JobSeeker::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->subscribeOptions = new SubscribeOptions();
        
        $this->progressBar = $this->getMockBuilder(ProgressBar::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->progressBarFactory = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $this->progressBarFactory->method('__invoke')
            ->willReturn($this->progressBar);
        
        $this->controller = $this->getMockBuilder(ConsoleController::class)
            ->setConstructorArgs([
                $this->searchProfileRepository,
                $this->jobSeeker,
                $this->mailer,
                $this->subscribeOptions,
                $this->progressBarFactory
            ])
            ->setMethods(['params'])
            ->getMock();
    }
    
    /**
     * @covers ::__construct()
     * @covers ::sendAction()
     */
    public function testSendActionWithoutAnyProfile()
    {
        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cursor->expects($this->once())
            ->method('count')
            ->willReturn(0);
        $cursor->expects($this->never())
            ->method('rewind');
        
        $this->searchProfileRepository->expects($this->once())
            ->method('getProfilesToSend')
            ->with($this->callback(function($delay) {
                if (!$delay instanceof DateTime) {
                    return false;
                }
                
                $now = new DateTime();
                $now->modify(sprintf('-%d minute', $this->subscribeOptions->getSearchJobsDelay()));
                
                return $this->compareDates($now, $delay);
            }), $this->equalTo(30))
            ->willReturn($cursor);
        
        $this->assertContains('no search profile', $this->controller->sendAction());
    }
    
    /**
     * @covers ::sendAction()
     */
    public function testSendActionWithLimitSet()
    {
        $limit = 10;
        
        $this->controller->method('params')
            ->will($this->returnValueMap([
                ['limit', $limit]
            ]));
        
        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cursor->expects($this->once())
            ->method('count')
            ->willReturn(0);
        
        $this->searchProfileRepository->expects($this->once())
            ->method('getProfilesToSend')
            ->with($this->anything(), $this->equalTo($limit))
            ->willReturn($cursor);
        
        $this->controller->sendAction();
    }
    
    /**
     * @covers ::sendAction()
     */
    public function testSendActionWithProfiles()
    {
        $profiles = [
            new SearchProfile(),
            new SearchProfile()
        ];
        $jobs = [
            [],
            [new stdClass()]
        ];
        $count = count($profiles);
        $maxJobsPerMail = $this->subscribeOptions->getMaxJobsPerMail();
        $serverUrl = 'https://domain.tld';
        
        $this->controller->method('params')
            ->will($this->returnValueMap([
                ['server-url', $serverUrl]
            ]));
        
        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cursor->expects($this->once())
            ->method('count')
            ->willReturn($count);
        $cursor->expects($this->exactly($count + 1))
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);
        $cursor->expects($this->exactly($count))
            ->method('current')
            ->willReturnOnConsecutiveCalls($profiles[0], $profiles[1]);
        
        $objectManager = $this->getMockBuilder(ObjectManager::class)
            ->getMock();
        $objectManager->expects($this->once())
            ->method('flush');
        
        $this->searchProfileRepository->expects($this->once())
            ->method('getProfilesToSend')
            ->willReturn($cursor);
        $this->searchProfileRepository->expects($this->once())
            ->method('getDocumentManager')
            ->willReturn($objectManager);
        
        $this->jobSeeker->expects($this->exactly($count))
            ->method('getProfileJobs')
            ->withConsecutive(
                [$this->identicalTo($profiles[0]), $this->identicalTo($maxJobsPerMail)],
                [$this->identicalTo($profiles[1]), $this->identicalTo($maxJobsPerMail)]
            )
            ->willReturnOnConsecutiveCalls($jobs[0], $jobs[1]);
        
        $this->mailer->expects($this->once())
            ->method('sendJobs')
            ->with($this->identicalTo($profiles[1]), $this->identicalTo($jobs[1]), $this->identicalTo($serverUrl));
        
        $this->progressBar->expects($this->exactly($count + 1))
            ->method('update')
            ->withConsecutive([1, 'Profile 1 / 2'], [2, 'Profile 2 / 2'], [3, 'Flushing to database ...']);
        
        $now = new DateTime();
        $this->assertEmpty(trim($this->controller->sendAction()));
        $this->assertNotNull($profiles[0]->getDateLastSearch());
        $this->assertTrue($this->compareDates($now, $profiles[0]->getDateLastSearch()));
        $this->assertNotNull($profiles[1]->getDateLastSearch());
        $this->assertTrue($this->compareDates($now, $profiles[1]->getDateLastSearch()));
        
        $this->assertNull($profiles[0]->getDateLastMail());
        $this->assertNotNull($profiles[1]->getDateLastMail());
        $this->assertTrue($this->compareDates($now, $profiles[1]->getDateLastMail()));
    }
    
    /**
     * @covers ::sendAction()
     */
    public function testSendActionWithMoreThanHundredProfiles()
    {
        $profiles = [];
        $profilePrototype = new SearchProfile();
        
        for ($i = 0; $i < 213; $i++) {
            $profiles[] = clone $profilePrototype;
        }
        
        $count = count($profiles);
        $flushCallsOnBatch = (int)floor($count / 100);
        
        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cursor->expects($this->once())
            ->method('count')
            ->willReturn($count);
        $cursor->expects($this->exactly($count + 1))
            ->method('valid')
            ->willReturnCallback(function () use (&$profiles) {
                $profile = current($profiles);
                return false !== $profile;
            });
        $cursor->expects($this->exactly($count))
            ->method('current')
            ->willReturnCallback(function () use (&$profiles) {
                $profile = current($profiles);
                next($profiles);
                return $profile;
            });
        
        $objectManager = $this->getMockBuilder(ObjectManager::class)
            ->getMock();
        $objectManager->expects($this->exactly($flushCallsOnBatch + 1))
            ->method('flush');
        
        $this->searchProfileRepository->expects($this->once())
            ->method('getProfilesToSend')
            ->willReturn($cursor);
        $this->searchProfileRepository->expects($this->once())
            ->method('getDocumentManager')
            ->willReturn($objectManager);
        
        $this->jobSeeker->expects($this->exactly($count))
            ->method('getProfileJobs');
        
        $this->progressBar->expects($this->exactly($count + $flushCallsOnBatch + 1))
            ->method('update');
        
        $this->assertEmpty(trim($this->controller->sendAction()));
    }
    
    /**
     * @covers ::cleanupAction()
     */
    public function testCleanupAction()
    {
        $totalRemoved = 325;
        
        $this->searchProfileRepository->expects($this->once())
            ->method('removeInactiveProfiles')
            ->with($this->callback(function($expiration) {
                if (!$expiration instanceof DateTime) {
                    return false;
                }
                
                $now = new DateTime();
                $now->modify(sprintf('-%d minute', $this->subscribeOptions->getInactiveProfileExpiration()));
                
                return $this->compareDates($now, $expiration);
            }))
            ->willReturn($totalRemoved);
        
        $this->assertContains('Total search profiles removed: ' . $totalRemoved, $this->controller->cleanupAction());
    }
    
    /**
     * @covers ::getProgressBarFactory()
     */
    public function testGetProgressBarFactory()
    {
        $this->assertSame($this->progressBarFactory, $this->controller->getProgressBarFactory());
    }
    
    /**
     * @param DateTime $first
     * @param DateTime $second
     * @return boolean
     */
    private function compareDates(DateTime $first, DateTime $second)
    {
        $deviation = 3;
                
        return abs($first->getTimestamp() - $second->getTimestamp()) <= $deviation;
    }
}
