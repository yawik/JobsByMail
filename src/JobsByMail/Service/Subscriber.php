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

class Subscriber
{
    
    /**
     * @param string $email
     * @param array $query
     * @return SearchProfileInterface
     */
    public function subscribe($email, array $query)
    {
        // implementation goes here ...
    }
}