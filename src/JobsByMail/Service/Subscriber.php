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
     * @param SearchProfileRepository $searchProfileRepository
     * @param callable $paginatorFactory
     */
    public function __construct(SearchProfileRepository $searchProfileRepository, callable $paginatorFactory)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->paginatorFactory = $paginatorFactory;
    }
    
    /**
     * @param string $email
     * @param array $query
     * @return SearchProfileInterface
     */
    public function subscribe($email, array $query)
    {
        /** @var \JobsByMail\Entity\SearchProfileInterface $searchProfile */
        $searchProfile = $this->searchProfileRepository->findOneByEmail($email);
        
        if (! $searchProfile) {
            // create a new search profile
            $searchProfile = $this->searchProfileRepository->create([
                'email' => $email
            ], true);
        }

        // update a profile
        $now = new DateTime();
        $searchProfile->setDateLastMail($now)
            ->setDateLastSearch($now)
            ->setQuery($query);
        
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
        
        /** @var \Zend\Paginator\Paginator $paginator */
        $paginator = call_user_func($this->paginatorFactory, 'Jobs/Board', [], $params);

        return $paginator->getAdapter()->getItems(0, $limit);
    }
}