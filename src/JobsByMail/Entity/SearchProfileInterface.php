<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013 - 2017 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace JobsByMail\Entity;

use Core\Entity\MetaDataProviderInterface;
use Core\Entity\EntityInterface;
use Core\Entity\IdentifiableEntityInterface;
use Core\Entity\ModificationDateAwareEntityInterface;

/**
 * Interface for a Search Profile
 *
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @package Jobs\Entity
 */
interface SearchProfileInterface extends
    EntityInterface,
    IdentifiableEntityInterface,
    ModificationDateAwareEntityInterface,
    MetaDataProviderInterface
{

    /**
     * Gets the date of the last search
     *
     * @return string
     */
    public function getDateLastSearch();

    /**
     * Sets the date of the last search
     *
     * @param $dateLastSearch
     * @return $this
     */
    public function setDateLastSearch($dateLastSearch);


    /**
     * Gets the end date of the last sent mail
     *
     * @return string
     */
    public function getDateLastMail();

    /**
     * Sets the end date for the last mail sent
     *
     * @param $dateLastMail
     * @return $this
     */
    public function setDateLastMail($dateLastMail);

    /**
     * Sets the email of a search profile
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
    
    /**
     * Gets the email of a search profile
     *
     * @return string
     */
    public function getEmail();

    /**
     * Sets the query of a search profile. Stores the form data of a search
     *
     * @param hash $query
     * @return $this
     */
    public function setQuery($query);

    /**
     * Gets the email of a search profile. Gets the form data of a search
     *
     * @return hash
     */
    public function getQuery();
}
