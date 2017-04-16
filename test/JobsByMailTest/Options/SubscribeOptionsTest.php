<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Options;

use JobsByMail\Options\SubscribeOptions;

/**
 * @coversDefaultClass \JobsByMail\Options\SubscribeOptions
 */
class SubscribeOptionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SubscribeOptions
     */
    private $subscribeOptions;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->subscribeOptions = new SubscribeOptions();
    }

    public function testDefaultFields()
    {
        $expected = [
            'q' => [
                'enabled' => true
            ],
            'l' => [
                'enabled' => true
            ],
            'd' => [
                'enabled' => true
            ]
        ];
        
        $this->assertSame($expected, $this->subscribeOptions->getFields());
    }
    
    /**
     * @covers ::getSearchJobsDelay()
     */
    public function testGetSearchJobsDelay()
    {
        $this->assertSame(1440, $this->subscribeOptions->getSearchJobsDelay());
        
        $expected = 123;
        $this->subscribeOptions->setSearchJobsDelay($expected);
        $this->assertSame($expected, $this->subscribeOptions->getSearchJobsDelay());
    }
    
    /**
     * @covers ::setSearchJobsDelay()
     */
    public function testSetSearchJobsDelay()
    {
        $expected = 123;
        $this->assertSame($this->subscribeOptions, $this->subscribeOptions->setSearchJobsDelay($expected));
        $this->assertSame($expected, $this->subscribeOptions->getSearchJobsDelay());
    }
    
    /**
     * @covers ::getInactiveProfileExpiration()
     */
    public function testGetInactiveProfileExpiration()
    {
        $this->assertSame(720, $this->subscribeOptions->getInactiveProfileExpiration());
        
        $expected = 123;
        $this->subscribeOptions->setInactiveProfileExpiration($expected);
        $this->assertSame($expected, $this->subscribeOptions->getInactiveProfileExpiration());
    }
    
    /**
     * @covers ::setInactiveProfileExpiration()
     */
    public function testSetInactiveProfileExpiration()
    {
        $expected = 123;
        $this->assertSame($this->subscribeOptions, $this->subscribeOptions->setInactiveProfileExpiration($expected));
        $this->assertSame($expected, $this->subscribeOptions->getInactiveProfileExpiration());
    }
    
    /**
     * @covers ::getMaxJobsPerMail()
     */
    public function testGetMaxJobsPerMail()
    {
        $this->assertSame(10, $this->subscribeOptions->getMaxJobsPerMail());
        
        $expected = 123;
        $this->subscribeOptions->setMaxJobsPerMail($expected);
        $this->assertSame($expected, $this->subscribeOptions->getMaxJobsPerMail());
    }
    
    /**
     * @covers ::setMaxJobsPerMail()
     */
    public function testSetMaxJobsPerMail()
    {
        $expected = 123;
        $this->assertSame($this->subscribeOptions, $this->subscribeOptions->setMaxJobsPerMail($expected));
        $this->assertSame($expected, $this->subscribeOptions->getMaxJobsPerMail());
    }
}
