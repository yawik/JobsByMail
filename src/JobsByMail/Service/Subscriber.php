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
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use Jobs\Entity\Location;
use DateTime;

class Subscriber
{
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var callable
     */
    private $paginatorFactory;
    
    /**
     * @var Location
     */
    private $locationPrototype;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param callable $paginatorFactory
     * @param Location $locationPrototype
     */
    public function __construct(SearchProfileRepository $searchProfileRepository, callable $paginatorFactory, Location $locationPrototype)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->paginatorFactory = $paginatorFactory;
        $this->locationPrototype = $locationPrototype;
    }
    
    /**
     * @param string $email
     * @param array $query
     * @param string $language
     * @return SearchProfileInterface
     */
    public function subscribe($email, array $query, $language)
    {
        /** @var \JobsByMail\Entity\SearchProfileInterface $searchProfile */
        $searchProfile = $this->searchProfileRepository->findOneByEmail($email);
        
        if (!$searchProfile) {
            // create a new search profile
            $searchProfile = $this->searchProfileRepository->create([
                'email' => $email
            ], true);
        }

        // update a profile
        $now = new DateTime();
        $searchProfile->setDateLastMail($now)
            ->setDateLastSearch($now)
            ->setQuery($query)
            ->setLanguage($language);
        
        return $searchProfile;
    }
    
    /**
     * @param SearchProfileInterface $searchProfile
     * @param int $limit
     * @return array
     */
    public function getRelevantJobs(SearchProfileInterface $searchProfile, $limit)
    {
        $params = $searchProfile->getQuery();
        $params['publishedSince'] = $searchProfile->getDateLastMail();
        
        if (isset($params['l']) && $params['l']) {
            $params['l'] = $this->getLocationEntity()->fromString($params['l']);
        }
        
        /** @var \Zend\Paginator\Paginator $paginator */
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