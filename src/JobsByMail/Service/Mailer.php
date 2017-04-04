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

class Mailer
{
    
    /**
     * @var MailService
     */
    private $mailService;
    
    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;
    
    /**
     * @var OrganizationImageCache
     */
    protected $organizationImageCache;
    
    /**
     * @param MailService $mailService
     * @param ModuleOptions $moduleOptions
     * @param OrganizationImageCache $organizationImageCache
     */
    public function __construct(MailService $mailService, ModuleOptions $moduleOptions, OrganizationImageCache $organizationImageCache)
    {
        $this->mailService = $mailService;
        $this->moduleOptions = $moduleOptions;
        $this->organizationImageCache = $organizationImageCache;
    }

    /**
     *
     * @param SearchProfileInterface $searchProfile
     * @param array $jobs
     */
    public function sendMail(SearchProfileInterface $searchProfile, array $jobs)
    {
        $url = parse_url($this->moduleOptions->getOperator()['homepage']);
        
        /** @var \Core\Mail\HTMLTemplateMessage $message */
        $message = $this->mailService->get('htmltemplate')
            ->setSubject('New jobs for you on %s', $this->moduleOptions->getSiteName())
            ->setTo($searchProfile->getEmail())
            ->setTemplate('jobs-by-mail/mail/jobs')
            ->setVariable('profile', $searchProfile)
            ->setVariable('jobs', $jobs)
            ->setVariable('host', $url['host'])
            ->setVariable('scheme', $url['scheme'])
            ->setVariable('organizationImageCache', $this->organizationImageCache);
        
        $this->mailService->send($message);
    }
}