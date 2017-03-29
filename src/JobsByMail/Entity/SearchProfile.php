<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.29
 */
namespace JobsByMail\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Core\Entity\ModificationDateAwareEntityTrait;
use Core\Entity\MetaDataProviderTrait;
use DateTime;
use InvalidArgumentException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="jobsbymail.search.profile", repositoryClass="\JobsByMail\Repository\SearchProfile")
 * @ODM\Indexes({
 *     @ODM\Index(keys={"dateLastSearch.date"="asc"}),
 *     @ODM\Index(keys={"dateLastMail.date"="asc"})
 * })
 */
class SearchProfile extends AbstractIdentifiableEntity implements SearchProfileInterface
{
    
    use ModificationDateAwareEntityTrait,
        MetaDataProviderTrait;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @ODM\Index(unique=true)
     */
    protected $email;
    
    /**
     * @var array
     * @ODM\Hash
     */
    protected $query = [];
    
    /**
     * @var DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateLastSearch;
    
    /**
     * @var DateTime
     * @ODM\Field(type="tz_date")
     */
    protected $dateLastMail;
        
    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::getDateLastSearch()
     */
    public function getDateLastSearch()
    {
        return $this->dateLastSearch;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::setDateLastSearch()
     */
    public function setDateLastSearch($dateLastSearch)
    {
        if (is_string($dateLastSearch)) {
            $dateLastSearch = new DateTime($dateLastSearch);
        } elseif (! $dateLastSearch instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('Expected object of type "%s"', DateTime::class));
        }
        
        $this->dateLastSearch= $dateLastSearch;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::getDateLastMail()
     */
    public function getDateLastMail()
    {
        return $this->dateLastMail;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::setDateLastMail()
     */
    public function setDateLastMail($dateLastMail)
    {
        if (is_string($dateLastMail)) {
            $dateLastMail = new DateTime($dateLastMail);
        } elseif (! $dateLastMail instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('Expected object of type "%s"', DateTime::class));
        }
        
        $this->dateLastMail= $dateLastMail;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::setEmail()
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::getEmail()
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::setQuery()
     */
    public function setQuery(array $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::getQuery()
     */
    public function getQuery()
    {
        return $this->query;
    }
}
