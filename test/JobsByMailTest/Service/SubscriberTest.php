<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Service;

use JobsByMail\Service\Subscriber;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Entity\SearchProfile;
use DateTime;

/**
 * @coversDefaultClass \JobsByMail\Service\Subscriber
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Subscriber
     */
    private $subscriber;
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->searchProfileRepository = $this->getMockBuilder(SearchProfileRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->subscriber = new Subscriber($this->searchProfileRepository);
    }
    
    /**
     * @covers ::__construct()
     * @covers ::subscribe()
     */
    public function testSubscribe()
    {
        $email = 'user@domain.tld';
        $query = ['key' => 'value'];
        $language = 'en';
        $now = new DateTime();
        $searchProfile = new SearchProfile();
        
        $this->searchProfileRepository->expects($this->once())
            ->method('create')
            ->with(null, true)
            ->willReturn($searchProfile);
        
        $this->assertSame($searchProfile, $this->subscriber->subscribe($email, $query, $language, $now));
        $this->assertSame($email, $searchProfile->getEmail());
        $this->assertSame($query, $searchProfile->getQuery());
        $this->assertSame($language, $searchProfile->getLanguage());
        $this->assertEquals($now, $searchProfile->getDateLastSearch());
        $this->assertEquals($now, $searchProfile->getDateLastMail());
    }
    
    /**
     * @covers ::unsubscribe()
     */
    public function testUnsubscribe()
    {
        $searchProfile = new SearchProfile();
        
        $this->searchProfileRepository->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($searchProfile));
        
        $this->assertNull($this->subscriber->unsubscribe($searchProfile));
    }
    
    /**
     * @covers ::confirm()
     */
    public function testConfirm()
    {
        $searchProfile = new SearchProfile();
        
        $this->assertTrue($searchProfile->isDraft());
        $this->assertNull($this->subscriber->confirm($searchProfile));
        $this->assertFalse($searchProfile->isDraft());
    }
}
