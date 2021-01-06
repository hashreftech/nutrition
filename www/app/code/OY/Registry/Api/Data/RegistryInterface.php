<?php
namespace OY\Registry\Api\Data;

use Magento\Catalog\Model\Product\Filter\DateTime;

interface RegistryInterface
{
    const ID = 'entity_id';
    const CUSTOMER_ID = 'customer_id';
    const DATE_TIME = 'date_time';
    const FULLNAME = 'fullname';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @return DateTime
     */
    public function getDateTime();

    /**
     * @return string
     */
    public function getFullname();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @param DateTime $dateTime
     * @return $this
     */
    public function setDateTime($dateTime);

    /**
     * @param string $fullname
     * @return $this
     */
    public function setFullname($fullname);


}
