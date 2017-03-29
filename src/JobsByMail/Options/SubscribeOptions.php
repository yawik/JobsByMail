<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Options;

use Core\Options\FieldsetCustomizationOptions;

class SubscribeOptions extends FieldsetCustomizationOptions
{

    /**
     * Fields can be disabled.
     *
     * @var array
     */
    protected $fields = [
        'q' => [
            'enabled' => true
        ],
        'l' => [
            'enabled' => true
        ],
        'd' => [
            'enabled' => true
        ]
    ];

    /**
     * Delay when to proceed another job search in minutes
     *
     * @var integer
     */
    protected $searchJobsDelay = 1440;

    /**
     * Maximum number of jobs sent to single email
     *
     * @var integer
     */
    protected $maxJobsPerMail = 10;

    /**
     * @return integer
     */
    public function getSearchJobsDelay()
    {
        return $this->searchJobsDelay;
    }

    /**
     * @param integer $searchJobsDelay
     * @return JobsByMail\Options$SubscribeOptions
     */
    public function setSearchJobsDelay($searchJobsDelay)
    {
        $this->searchJobsDelay = $searchJobsDelay;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxJobsPerMail()
    {
        return $this->maxJobsPerMail;
    }

    /**
     * @param integer $maxJobsPerMail
     * @return JobsByMail\Options$SubscribeOptions
     */
    public function setMaxJobsPerMail($maxJobsPerMail)
    {
        $this->maxJobsPerMail = $maxJobsPerMail;
        return $this;
    }
}