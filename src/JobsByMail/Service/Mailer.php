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
use Core\Mail\MailService;
use Core\Options\ModuleOptions;
use Organizations\ImageFileCache\Manager as OrganizationImageCache;
use LogicException;
use Zend\Mail\Exception\ExceptionInterface as MailException;
use Zend\Log\LoggerInterface as Log;
use Zend\Mail\Message;
use JobsByMail\Service\Hash;

class Mailer
{
    
    /**
     * @var MailService
     */
    private $mailService;
    
    /**
     * @var Hash
     */
    private $hash;
    
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    
    /**
     * @var OrganizationImageCache
     */
    protected $organizationImageCache;
    
    /**
     * @var Log
     */
    protected $log;
    
    /**
     * @param MailService $mailService
     * @param Hash $hash
     * @param ModuleOptions $moduleOptions
     * @param OrganizationImageCache $organizationImageCache
     * @param Log $log
     */
    public function __construct(
        MailService $mailService,
        Hash $hash,
        ModuleOptions $moduleOptions,
        OrganizationImageCache $organizationImageCache,
        Log $log)
    {
        $this->mailService = $mailService;
        $this->hash = $hash;
        $this->moduleOptions = $moduleOptions;
        $this->organizationImageCache = $organizationImageCache;
        $this->log = $log;
    }

    /**
     *
     * @param SearchProfileInterface $searchProfile
     * @param array $jobs
     * @param string $serverUrl
     * @return boolean
     */
    public function sendJobs(SearchProfileInterface $searchProfile, array $jobs, $serverUrl = null)
    {
        $serverUrl = parse_url($serverUrl ?: $this->moduleOptions->getOperator()['homepage']);
        
        /** @var \Core\Mail\HTMLTemplateMessage $message */
        $message = $this->mailService->get('htmltemplate')
            ->setSubject('New jobs for you on %s', $this->moduleOptions->getSiteName())
            ->setTo($searchProfile->getEmail())
            ->setTemplate('jobs-by-mail/mail/jobs')
            ->setVariable('searchProfile', $searchProfile)
            ->setVariable('jobs', $jobs)
            ->setVariable('host', $serverUrl['host'])
            ->setVariable('scheme', $serverUrl['scheme'])
            ->setVariable('hash', $this->hash)
            ->setVariable('organizationImageCache', $this->organizationImageCache);
        
        return $this->sendMessage($message);
    }

    /**
     * @param SearchProfileInterface $searchProfile
     * @throws LogicException
     * @return boolean
     */
    public function sendConfirmation(SearchProfileInterface $searchProfile)
    {
        if (!$searchProfile->isDraft()) {
            throw new LogicException('search profile is not a draft');
        }
        
        /** @var \Core\Mail\HTMLTemplateMessage $message */
        $message = $this->mailService->get('htmltemplate')
            ->setSubject('Confirm your search profile on %s', $this->moduleOptions->getSiteName())
            ->setTo($searchProfile->getEmail())
            ->setTemplate('jobs-by-mail/mail/confirmation')
            ->setVariable('searchProfile', $searchProfile)
            ->setVariable('hash', $this->hash);
        
        return $this->sendMessage($message);
    }
    
    /**
     * @param Message $message
     * @return boolean
     */
    private function sendMessage(Message $message)
    {
        try {
            $this->mailService->send($message);
            return true;
        } catch (MailException $e) {
            $this->log->err((string) $e);
            return false;
        }
    }
}