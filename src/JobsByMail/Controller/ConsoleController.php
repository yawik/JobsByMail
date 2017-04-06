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
use JobsByMail\Service\Subscriber;
use JobsByMail\Service\Mailer;
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
     * @var Subscriber
     */
    private $subscriber;
    
    /**
     * @var Mailer
     */
    private $mailer;
    
    /**
     * @var callable
     */
    protected $progressBarFactory;
    
    /**
     * @param SearchProfileRepository $searchProfileRepository
     * @param Subscriber $subscriber
     * @param Mailer $mailer
     * @param SubscribeOptions $subscribeOptions
     * @param callable $progressBarFactory
     */
    public function __construct(
        SearchProfileRepository $searchProfileRepository,
        Subscriber $subscriber,
        Mailer $mailer,
        SubscribeOptions $subscribeOptions,
        callable $progressBarFactory
    )
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->subscriber = $subscriber;
        $this->mailer = $mailer;
        $this->subscribeOptions = $subscribeOptions;
        $this->progressBarFactory = $progressBarFactory;
    }
    
    public function sendAction()
    {
        $limit = abs($this->params('limit')) ?: 30;
        
        // select all profiles which have not been checked recently
        $delay = $this->subscribeOptions->getSearchJobsDelay();
        $searchProfiles = $this->searchProfileRepository->getProfilesToCheck($delay, $limit);
        $documentManager = $this->searchProfileRepository->getDocumentManager();
        
        $i = 1;
        $count = count($searchProfiles);
        $progressBarFactory = $this->progressBarFactory;
        /** @var \Core\Console\ProgressBar $progressBar */
        $progressBar = $progressBarFactory($count);
        
        // iterate profiles and send them e-mails with jobs if any
        foreach ($searchProfiles as $searchProfile) {
            $now = new DateTime();
            $jobs = $this->subscriber->getRelevantJobs($searchProfile, $this->subscribeOptions->getMaxJobsPerMail());
            
            if ($jobs) {
                // sent mail
                $this->mailer->sendMail($searchProfile, $jobs);
                
                // set date of sending a mail
                $searchProfile->setDateLastMail($now);
            }
            
            // set date of the last search
            $searchProfile->setDateLastSearch($now);
            
            if (0 === $i % 100) {
                $progressBar->update($i, 'Flushing to database ...');
                $documentManager->flush();
            }
            
            $progressBar->update($i, 'Profile ' . $i . ' / ' . $count);
            $i++;
        }
        
        $progressBar->update($i, 'Flushing to database ...');
        $documentManager->flush();
        $progressBar->finish();
    }
}
