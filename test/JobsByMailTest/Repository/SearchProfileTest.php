<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Repository;

use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\ODM\MongoDB\Query\Query;
use DateTime;
use stdClass;

/**
 * @coversDefaultClass \JobsByMail\Repository\SearchProfile
 */
class SearchProfileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;
    
    /**
     * @var Query
     */
    private $query;
    
    /**
     * @see \PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->queryBuilder->method('getQuery')
            ->willReturn($this->query);
        
        $this->searchProfileRepository = $this->getMockBuilder(SearchProfileRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['createQueryBuilder'])
            ->getMock();
        $this->searchProfileRepository->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);
        
    }

    /**
     * @covers ::getProfilesToSend()
     * @dataProvider dataGetProfilesToSend
     */
    public function testGetProfilesToSend($limit)
    {
        $result = new stdClass();
        $delay = new DateTime();
        
        $this->queryBuilder->expects($this->exactly(2))
            ->method('field')
            ->withConsecutive(['isDraft'], ['dateLastSearch.date'])
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('equals')
            ->with(false)
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('lt')
            ->with($delay)
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('sort')
            ->with(['dateLastSearch.date' => 1])
            ->willReturnSelf();
        $this->queryBuilder->expects($this->exactly(isset($limit) ? 1 : 0))
            ->method('limit')
            ->with($limit)
            ->willReturnSelf();
        
        $this->query->expects($this->once())
            ->method('execute')
            ->willReturn($result);
        
        $this->assertSame($result, $this->searchProfileRepository->getProfilesToSend($delay, $limit));
    }

    /**
     * @covers ::removeInactiveProfiles()
     */
    public function testRemoveInactiveProfiles()
    {
        $removed = 3;
        $result = ['n' => $removed];
        $date = new DateTime();
        
        $this->queryBuilder->expects($this->once())
            ->method('remove')
            ->willReturnSelf();
        $this->queryBuilder->expects($this->exactly(2))
            ->method('field')
            ->withConsecutive(['isDraft'], ['dateLastSearch.date'])
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('equals')
            ->with(true)
            ->willReturnSelf();
        $this->queryBuilder->expects($this->once())
            ->method('lt')
            ->with($date)
            ->willReturnSelf();
        
        $this->query->expects($this->once())
            ->method('execute')
            ->willReturn($result);
        
        $this->assertSame($removed, $this->searchProfileRepository->removeInactiveProfiles($date));
    }
    
    /**
     * @return array
     */
    public function dataGetProfilesToSend()
    {
        return [
            [null],
            [10]
        ];
    }
}
