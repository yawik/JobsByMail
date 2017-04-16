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
     * @param SearchProfileRepository $searchProfileRepository
     */
    public function __construct(SearchProfileRepository $searchProfileRepository)
    {
        $this->searchProfileRepository = $searchProfileRepository;
    }
    
    /**
     * @param string $email
     * @param array $query
     * @param string $language
     * @param DateTime $now
     * @return SearchProfileInterface
     */
    public function subscribe($email, array $query, $language, DateTime $now = null)
    {
        $now = $now ?: new DateTime();
        $searchProfile = $this->searchProfileRepository->create(null, true)
            ->setEmail($email)
            ->setDateLastMail($now)
            ->setDateLastSearch($now)
            ->setQuery($query)
            ->setLanguage($language);
        
        return $searchProfile;
    }
    
    /**
     * @param SearchProfileInterface $searchProfile
     */
    public function unsubscribe(SearchProfileInterface $searchProfile)
    {
        $this->searchProfileRepository->remove($searchProfile);
    }
    
    /**
     * @param SearchProfileInterface $searchProfile
     */
    public function confirm(SearchProfileInterface $searchProfile)
    {
        $searchProfile->setIsDraft(false);
    }
}