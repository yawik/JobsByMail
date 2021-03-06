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
use Core\Entity\DraftableEntityTrait;
use DateTime;
use Exception;
use InvalidArgumentException;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="jobsbymail.search.profile", repositoryClass="\JobsByMail\Repository\SearchProfile")
 * @ODM\Indexes({
 *     @ODM\Index(keys={"dateLastSearch.date"="asc"}),
 *     @ODM\Index(keys={"dateLastMail.date"="asc"}),
 *     @ODM\Index(keys={"isDraft"="asc"})
 * })
 */
class SearchProfile extends AbstractIdentifiableEntity implements SearchProfileInterface
{
    
    use ModificationDateAwareEntityTrait,
        MetaDataProviderTrait,
        DraftableEntityTrait;

    /**
     * @var string
     * @ODM\Field(type="string")
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
     * Language as ISO 639-1
     *
     * @var string
     * @ODM\Field(type="string")
     */
    protected $language;
        
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
        $this->dateLastSearch = $this->parseDate($dateLastSearch);
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
        $this->dateLastMail = $this->parseDate($dateLastMail);
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

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::getLanguage()
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * {@inheritDoc}
     * @see \JobsByMail\Entity\SearchProfileInterface::setLanguage()
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param DateTime|string $date
     * @throws InvalidArgumentException
     * @return \DateTime
     */
    private function parseDate($date)
    {
        if (is_string($date)) {
            try {
                $date = new DateTime($date);
            } catch (Exception $e) {
                throw new InvalidArgumentException(sprintf('Cannot parse date "%s"', $date), null, $e);
            }
        } elseif (!$date instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('Expected object of type "%s"', DateTime::class));
        }
        
        return $date;
    }
}
