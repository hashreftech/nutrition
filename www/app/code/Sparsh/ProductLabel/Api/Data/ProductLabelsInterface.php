<?php
/**
 * Sparsh ProductLabel Module
 * php version 7.0.31
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
namespace Sparsh\ProductLabel\Api\Data;

/**
 * Interface ProductLabelsInterface
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
interface ProductLabelsInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const RULE_ID= 'rule_id';
    const NAME = 'name';
    const LABEL = 'label';
    const IS_ACTIVE     = 'is_active';
    const SORT_ORDER = 'sort_order';
    const TYPE = 'type';
    const PRODUCT_LABEL = 'product_label';
    const PRODUCT_LABEL_BACKGROUND_COLOR = 'product_label_background_color';
    const PRODUCT_LABEL_COLOR = 'product_label_color';
    const PRODUCT_LABEL_SELECT = 'product_label_select';
    const PRODUCT_LABEL_SHAPE = 'product_label_shape';
    const PRODUCT_LABEL_IMAGE = 'product_label_image';
    const FROM_DATE                 = 'from_date';
    const TO_DATE                   = 'to_date';
    const PRIORITY                   = 'priority';
    const CONDITIONS_SERIALIZZED = 'conditions_serialized';
    const CREATION_TIME = 'creation_time';
    const UPDATION_TIME   = 'updation_time';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Name
     *
     * @return string
     */
    public function getName();

    /**
     * Get Label
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Get Sort Order
     *
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Get Product Label
     *
     * @return string|null
     */
    public function getProductLabel();

    /**
     * Get Product Backgroundcolor
     *
     * @return string|null
     */

    public function getProductLabelBackgroundColor();

    /**
     * Get Product Label color
     *
     * @return string|null
     */
     public function getProductLabelColor();

    /**
     * Get Product Label Select type
     *
     * @return string|null
     */
   public function getProductLabelSelect();

   /**
     * Get Product Label Select shape
     *
     * @return string|null
     */

   public function getProductLabelShape();

   /**
     * Get Product Label Image
     *
     * @return string|null
     */
   public function getProductLabelImage();

    /**
     * Get the from date.
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set the from date.
     *
     * @param string $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get the to date.
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set the to date.
     *
     * @param string $toDate
     * @return $this
     */
    public function setToDate($toDate);

    /**
     * Get the priority.
     *
     * @return string|null
     */
    public function getPriority();

    /**
     * Set the priority.
     *
     * @param string $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get Condition
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get updation time
     *
     * @return string|null
     */
    public function getUpdationTime();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setId($id);

    /**
     * Set Name
     *
     * @param string $name
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setName($name);

    /**
     * Set Label
     *
     * @param string $label
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setLabel($label);

    /**
     * Set is active
     *
     * @param int|bool $isActive
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setIsActive($isActive);

    /**
     * Set Sort Order
     *
     * @param int $sortorder
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setSortOrder($sortorder);

    /**
     * Set Type
     *
     * @param string $city
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setType($type);

    /**
     * Set Condition
     *
     * @param string $condition
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setConditionsSerialized($condition);

    /**
     * Set Product Label
     *
     * @param string $product_label
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabel($product_label);

    /**
     * Set Product Label Backgourd color
     *
     * @param string $product_label_background_color
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabelBackgroundColor($product_label_background_color);

    /**
     * Set Product Label Color
     *
     * @param string $product_label_color
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabelColor($product_label_color);

    /**
     * Set Product Label select type
     *
     * @param string $product_label_select
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabelSelect($product_label_select);

    /**
     * Set Product Label image
     *
     * @param string $product_label_shape
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabelShape($product_label_shape);

    /**
     * Set Product Label image
     *
     * @param string $product_label_image
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setProductLabelImage($product_label_image);

    /**
     * Set creation time
     *
     * @param time $creationTime
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set updation time
     *
     * @param time $updationTime
     * @return \Sparsh\ProductLabel\Api\Data\ProductLabelsInterface
     */
    public function setUpdationTime($updationTime);
}
