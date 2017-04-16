<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Repository;

use Core\Repository\AbstractRepository;
use DateTime;

class SearchProfile extends AbstractRepository
{

    /**
     * @param DateTime $searchDelay
     * @param int $limit
     * @return \Doctrine\ODM\MongoDB\Cursor
     */
    public function getProfilesToSend(DateTime $searchDelay, $limit = null)
    {
        $qb = $this->createQueryBuilder()
            ->field('isDraft')->equals(false)
            ->field('dateLastSearch.date')->lt($searchDelay)
            ->sort(['dateLastSearch.date' => 1]);
        
        if (isset($limit)) {
            $qb->limit($limit);
        }
        
        return $qb->getQuery()->execute();
    }
    
    /**
     * @param DateTime $expiration
     * @return int Number of removed search profiles
     */
    public function removeInactiveProfiles(DateTime $expiration)
    {
        $qb = $this->createQueryBuilder()
            ->remove()
            ->field('isDraft')->equals(true)
            ->field('dateLastSearch.date')->lt($expiration);
        
        return $qb->getQuery()->execute()['n'];
    }
}