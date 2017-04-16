<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Entity;

use JobsByMail\Entity\SearchProfile;
use DateTime;

/**
 * @coversDefaultClass \JobsByMail\Entity\SearchProfile
 */
class SearchProfileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SearchProfile
     */
    private $searchProfile;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->searchProfile = new SearchProfile();
    }

    /**
     * @covers ::getDateLastSearch()
     */
    public function testGetDateLastSearch()
    {
        $this->assertNull($this->searchProfile->getDateLastSearch());
        
        $expected = new DateTime();
        $this->searchProfile->setDateLastSearch($expected);
        $this->assertSame($expected, $this->searchProfile->getDateLastSearch());
    }

    /**
     * @covers ::setDateLastSearch()
     * @covers ::parseDate()
     * @dataProvider dataInvalidDate
     * @expectedException \InvalidArgumentException
     */
    public function testSetDateLastSearchInvalid($invalid)
    {
        $this->searchProfile->setDateLastSearch($invalid);
    }

    /**
     * @covers ::setDateLastSearch()
     * @covers ::parseDate()
     * @dataProvider dataValidDate
     */
    public function testSetDateLastSearchValid($date)
    {
        $this->assertSame($this->searchProfile, $this->searchProfile->setDateLastSearch($date));
        
        $expected = $date instanceof DateTime ? $date : new DateTime($date);
        $actual = $this->searchProfile->getDateLastSearch();
        $this->assertSame($expected->format(DateTime::ISO8601), $actual->format(DateTime::ISO8601));
    }
    
    /**
     * @covers ::getDateLastMail()
     */
    public function testGetDateLastMail()
    {
        $this->assertNull($this->searchProfile->getDateLastMail());
        
        $expected = new DateTime();
        $this->searchProfile->setDateLastMail($expected);
        $this->assertSame($expected, $this->searchProfile->getDateLastMail());
    }

    /**
     * @covers ::setDateLastMail()
     * @covers ::parseDate()
     * @dataProvider dataInvalidDate
     * @expectedException \InvalidArgumentException
     */
    public function testSetDateLastMailInvalid($invalid)
    {
        $this->searchProfile->setDateLastMail($invalid);
    }

    /**
     * @covers ::setDateLastMail()
     * @covers ::parseDate()
     * @dataProvider dataValidDate
     */
    public function testSetDateLastMailValid($date)
    {
        $this->assertSame($this->searchProfile, $this->searchProfile->setDateLastMail($date));
        
        $expected = $date instanceof DateTime ? $date : new DateTime($date);
        $actual = $this->searchProfile->getDateLastMail();
        $this->assertSame($expected->format(DateTime::ISO8601), $actual->format(DateTime::ISO8601));
    }
    
    /**
     * @covers ::getEmail()
     */
    public function testGetEmail()
    {
        $this->assertNull($this->searchProfile->getEmail());
        
        $expected = 'user@domain.tld';
        $this->searchProfile->setEmail($expected);
        $this->assertSame($expected, $this->searchProfile->getEmail());
    }
    
    /**
     * @covers ::setEmail()
     */
    public function testSetEmail()
    {
        $expected = 'user@domain.tld';
        $this->assertSame($this->searchProfile, $this->searchProfile->setEmail($expected));
        $this->assertSame($expected, $this->searchProfile->getEmail());
    }
    
    /**
     * @covers ::getQuery()
     */
    public function testGetQuery()
    {
        $this->assertSame([], $this->searchProfile->getQuery());
        
        $expected = ['key' => 'value'];
        $this->searchProfile->setQuery($expected);
        $this->assertSame($expected, $this->searchProfile->getQuery());
    }
    
    /**
     * @covers ::setQuery()
     */
    public function testSetQuery()
    {
        $expected = ['key' => 'value'];
        $this->assertSame($this->searchProfile, $this->searchProfile->setQuery($expected));
        $this->assertSame($expected, $this->searchProfile->getQuery());
    }
    
    /**
     * @covers ::getLanguage()
     */
    public function testGetLanguage()
    {
        $this->assertNull($this->searchProfile->getLanguage());
        
        $expected = 'en';
        $this->searchProfile->setLanguage($expected);
        $this->assertSame($expected, $this->searchProfile->getLanguage());
    }
    
    /**
     * @covers ::setLanguage()
     */
    public function testSetLanguage()
    {
        $expected = 'en';
        $this->assertSame($this->searchProfile, $this->searchProfile->setLanguage($expected));
        $this->assertSame($expected, $this->searchProfile->getLanguage());
    }
    
    /**
     * @return array
     */
    public function dataInvalidDate()
    {
        return [
            [null],
            [1],
            [new \stdClass()],
            ['invalid'],
        ];
    }
    
    /**
     * @return array
     */
    public function dataValidDate()
    {
        return [
            ['2017-04-16'],
            ['2017-04-16 10:46:03'],
            [new DateTime()],
        ];
    }
}
