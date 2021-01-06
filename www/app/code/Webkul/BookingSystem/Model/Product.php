<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_BookingSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\BookingSystem\Model;

use Webkul\BookingSystem\Api\Data\ProductInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Product extends \Magento\Framework\Model\AbstractModel implements ProductInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * BookingSystem Product  cache tag.
     */
    const CACHE_TAG = 'bookingsystem_product';

    /**
     * @var string
     */
    protected $_cacheTag = 'bookingsystem_product';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'bookingsystem_product';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\BookingSystem\Model\ResourceModel\Product::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePreorder();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Items.
     *
     * @return \Webkul\BookingSystem\Model\Product
     */
    public function noRouteItems()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\BookingSystem\Api\Data\ProductInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
