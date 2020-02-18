<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Service;

use JobsByMail\Entity\SearchProfileInterface;
use Jobs\Entity\Location;

class JobSeeker
{
    
    /**
     * @var callable
     */
    private $paginatorFactory;
    
    /**
     * @var Location
     */
    private $locationPrototype;
    
    /**
     * @param callable $paginatorFactory
     * @param Location $locationPrototype
     */
    public function __construct(callable $paginatorFactory, Location $locationPrototype)
    {
        $this->paginatorFactory = $paginatorFactory;
        $this->locationPrototype = $locationPrototype;
    }
    
    /**
     * @param SearchProfileInterface $searchProfile
     * @param int $limit
     * @return array
     */
    public function getProfileJobs(SearchProfileInterface $searchProfile, $limit)
    {
        $params = $searchProfile->getQuery();
        $params['publishedSince'] = $searchProfile->getDateLastMail();
        
        if (isset($params['l']) && $params['l']) {
            $params['l'] = $this->getLocationEntity()->fromString($params['l']);
        }
        
        /** @var \Laminas\Paginator\Paginator $paginator */
        $paginator = call_user_func($this->paginatorFactory, 'Jobs/Board', [], $params);

        return $paginator->getAdapter()->getItems(0, $limit);
    }
    
    /**
     * @return Location
     */
    protected function getLocationEntity()
    {
        return clone $this->locationPrototype;
    }
}