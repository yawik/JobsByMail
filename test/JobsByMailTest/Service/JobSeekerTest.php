<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */

namespace JobsByMailTest\Service;

use JobsByMail\Service\JobSeeker;
use Jobs\Entity\Location;
use JobsByMail\Entity\SearchProfile;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\AdapterInterface as PaginatorAdapter;
use Core\Entity\LocationInterface;
use DateTime;
use stdClass;

/**
 * @coversDefaultClass \JobsByMail\Service\JobSeeker
 */
class JobSeekerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct()
     * @covers ::getProfileJobs()
     * @covers ::getLocationEntity()
     */
    public function testGetProfileJobs()
    {
        $limit = 10;
        $coordinates = ['13.3888599', '52.5170365'];
        $query = [
            'q' => 'search',
            'l' => sprintf('{"coordinates":{"type":"Point","coordinates":[%s,%s]}}', $coordinates[0], $coordinates[1]),
            'd' => '50',
        ];
        $dateLastMail = new DateTime('2017-04-16 15:50:02');
        $searchProfile = (new SearchProfile())->setQuery($query)
            ->setDateLastMail($dateLastMail);
        $result = new stdClass();
        
        $paginatorAdapter = $this->getMockBuilder(PaginatorAdapter::class)
            ->getMock();
        $paginatorAdapter->expects($this->once())
            ->method('getItems')
            ->with(0, $limit)
            ->willReturn($result);
        
        $paginator = $this->getMockBuilder(Paginator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paginator->expects($this->once())
            ->method('getAdapter')
            ->willReturn($paginatorAdapter);
        
        $paginatorFactory = $this->getMockBuilder(stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();
        $paginatorFactory->expects($this->once())
            ->method('__invoke')
            ->with('Jobs/Board', [], $this->callback(function (array $params) use ($query, $coordinates, $dateLastMail)
            {
                if (!isset($params['q']) || $params['q'] != $query['q']) {
                    return false;
                }
                
                if (!isset($params['l']) || !$params['l'] instanceof LocationInterface) {
                    return false;
                }
                
                if ($params['l']->getCoordinates()->getCoordinates() != $coordinates) {
                    return false;
                }
                
                if (!isset($params['d']) || $params['d'] != $query['d']) {
                    return false;
                }
                
                if (!isset($params['publishedSince']) || $params['publishedSince'] != $dateLastMail) {
                    return false;
                }
                
                return true;
            }))
            ->willReturn($paginator);
        
        $jobSeeker = new JobSeeker($paginatorFactory, new Location());
        $this->assertSame($result, $jobSeeker->getProfileJobs($searchProfile, $limit));
    }
}
