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
use LogicException;

class Hash
{

    /**
     *
     * @param SearchProfileInterface $searchProfile
     * @return string
     */
    public function generate(SearchProfileInterface $searchProfile)
    {
        if (!$searchProfile->getId()) {
            throw new LogicException('profile must have an ID');
        }
        
        if (!$searchProfile->getEmail()) {
            throw new LogicException('profile must have an email');
        }
        
        return md5($searchProfile->getId() . $searchProfile->getEmail());
    }

    /**
     *
     * @param SearchProfileInterface $searchProfile
     * @param string $hash
     * @return boolean
     */
    public function validate(SearchProfileInterface $searchProfile, $hash)
    {
        return $this->generate($searchProfile) === $hash;
    }
}