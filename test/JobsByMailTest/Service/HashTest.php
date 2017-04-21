<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Service;

use JobsByMail\Service\Hash;
use JobsByMail\Entity\SearchProfile;

/**
 * @coversDefaultClass \JobsByMail\Service\Hash
 */
class HashTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Hash
     */
    private $hash;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->hash = new Hash();
    }

    /**
     * @covers ::generate()
     * @expectedException \LogicException
     * @expectedMessage profile must have an ID
     */
    public function testGenerateMissingId()
    {
        $this->hash->generate(new SearchProfile());
    }

    /**
     * @covers ::generate()
     * @expectedException \LogicException
     * @expectedMessage profile must have an email
     */
    public function testGenerateMissingEmail()
    {
        $this->hash->generate((new SearchProfile())->setId('id'));
    }

    /**
     * @covers ::generate()
     */
    public function testGenerate()
    {
        $searchProfile = (new SearchProfile())->setId('id')
            ->setEmail('user@domain.tld');
        $expected = md5($searchProfile->getId() . $searchProfile->getEmail());
        
        $this->assertSame($expected, $this->hash->generate($searchProfile));
    }
    
    /**
     * @covers ::validate()
     * @expectedException \LogicException
     * @expectedMessage profile must have an ID
     */
    public function testValidateMissingId()
    {
        $this->hash->validate(new SearchProfile(), 'someHash');
    }

    /**
     * @covers ::validate()
     * @expectedException \LogicException
     * @expectedMessage profile must have an email
     */
    public function testValidateMissingEmail()
    {
        $this->hash->validate((new SearchProfile())->setId('id'), 'someHash');
    }

    /**
     * @covers ::validate()
     */
    public function testValidateInvalidHash()
    {
        $searchProfile = (new SearchProfile())->setId('id')
            ->setEmail('user@domain.tld');
        
        $this->assertFalse($this->hash->validate($searchProfile, 'invalidHash'));
    }

    /**
     * @covers ::validate()
     */
    public function testValidateValidHash()
    {
        $searchProfile = (new SearchProfile())->setId('id')
            ->setEmail('user@domain.tld');
        $hash = md5($searchProfile->getId() . $searchProfile->getEmail());
        
        $this->assertTrue($this->hash->validate($searchProfile, $hash));
    }
}
