<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use JobsByMail\Repository\SearchProfile as SearchProfileRepository;
use JobsByMail\Options\SubscribeOptions;
use DateTime;


class ConsoleController extends AbstractActionController
{
    
    /**
     * @var SearchProfileRepository
     */
    private $searchProfileRepository;
    
    /**
     * @var SubscribeOptions
     */
    private $subscribeOptions;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param SubscribeOptions $subscribeOptions
     */
    public function __construct(SearchProfileRepository $searchProfileRepository, SubscribeOptions $subscribeOptions)
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->subscribeOptions = $subscribeOptions;
    }
    
    public function sendAction()
    {
        return 'not implemented yet ...'.PHP_EOL;
        
        // select all profiles which have not been checked recently
        $searchProfiles = $this->searchProfileRepository->getProfilesToCheck($this->subscribeOptions->getSearchJobsDelay());
        
        // iterate profiles and send them e-mails with jobs if any
        foreach ($searchProfiles as $searchProfile) {
            $jobs = $this->subscriberService->getRelevantJobs($searchProfile, $this->subscribeOptions->getMaxJobsPerMail());
            
            if ($jobs) {
                // sent mail
                // implement sending of mail ...
                
                // set date of sending a mail
                $searchProfile->setDateLastMail(new DateTime());
            }
            
            // set date of sending a mail
            $searchProfile->setDateLastSearch(new DateTime());
        }
    }
}
