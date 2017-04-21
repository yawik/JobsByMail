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
use JobsByMail\Service\JobSeeker;
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
     * @var JobSeeker
     */
    private $jobSeeker;
    
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
     * @param JobSeeker $jobSeeker
     * @param Mailer $mailer
     * @param SubscribeOptions $subscribeOptions
     * @param callable $progressBarFactory
     */
    public function __construct(
        SearchProfileRepository $searchProfileRepository,
        JobSeeker $jobSeeker,
        Mailer $mailer,
        SubscribeOptions $subscribeOptions,
        callable $progressBarFactory
    )
    {
        $this->searchProfileRepository = $searchProfileRepository;
        $this->jobSeeker = $jobSeeker;
        $this->mailer = $mailer;
        $this->subscribeOptions = $subscribeOptions;
        $this->progressBarFactory = $progressBarFactory;
    }
    
    public function sendAction()
    {
        $limit = abs($this->params('limit')) ?: 30;
        $delay = new DateTime();
        $delay->modify(sprintf('-%d minute', $this->subscribeOptions->getSearchJobsDelay()));
        
        // select all profiles which have not been checked recently
        $searchProfiles = $this->searchProfileRepository->getProfilesToSend($delay, $limit);
        $count = count($searchProfiles);
        
        if (0 === $count) {
            return 'There is no search profile to send email to.' . PHP_EOL;
        }
        
        $progressBarFactory = $this->progressBarFactory;
        /** @var \Core\Console\ProgressBar $progressBar */
        $progressBar = $progressBarFactory($count);
        $documentManager = $this->searchProfileRepository->getDocumentManager();
        $serverUrl = $this->params('server-url');
        $i = 1;
        
        // iterate profiles and send them e-mails with jobs if any
        foreach ($searchProfiles as $searchProfile) {
            $now = new DateTime();
            $jobs = $this->jobSeeker->getProfileJobs($searchProfile, $this->subscribeOptions->getMaxJobsPerMail());
            
            if ($jobs) {
                // sent mail
                $this->mailer->sendJobs($searchProfile, $jobs, $serverUrl);
                
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
    
    public function cleanupAction()
    {
        $expiration = new DateTime();
        $expiration->modify(sprintf('-%d minute', $this->subscribeOptions->getInactiveProfileExpiration()));
        
        $totalRemoved = $this->searchProfileRepository->removeInactiveProfiles($expiration);
        
        return sprintf('Total search profiles removed: %d.' . PHP_EOL, $totalRemoved);
    }

    /**
     * @return callable
     */
    public function getProgressBarFactory()
    {
        return $this->progressBarFactory;
    }
}
