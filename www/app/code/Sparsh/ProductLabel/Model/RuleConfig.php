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

namespace Sparsh\ProductLabel\Model;

use Sparsh\ProductLabel\Model\ResourceModel\ProductLabels\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

/**
 * Class RuleConfig
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class RuleConfig
{
    /**
     * @var Session
     */
    private $customerSesion;

    /**
     * RuleConfig constructor.
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param FilterProvider $filterProvider
     * @param Session $customerSession
     * @param \Sparsh\ProductLabel\Model\ProductLabelsFactory $productLabels
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSesion,
        ProductLabels $model,
        ProductLabelsFactory $productLabels
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSesion = $customerSesion;
        $this->model = $model;
        $this->productLabels = $productLabels;
      }

    /**
     * Get Product Tabs
     *
     * @return array
     * @throws \Exception
     */

    public function getLastAppiledRuleId($_product)
    {
        $ruleCollection = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('from_date', ['lteq' => date("Y-m-d H:i:s")])
            ->addFieldToFilter('to_date', [['null' => true],['gteq' => date("Y-m-d H:i:s")]])
            ->addFieldToFilter('is_active', ['eq' => 1])
            ->setOrder(
                'priority',
                'asc'
            );

        $ruleId = 0;
        if(!empty($_product)){
            foreach ($ruleCollection as $rule) {
                $matchedProductIds = [];
                if (!empty($rule->getConditionsSerialized())) {
                    $matchedProductId = $this->productLabels->create()->setData('conditions_serialized', $rule->getConditionsSerialized());

                    if ($matchedProductId->getConditions()->validate($_product)) {
                        $matchedProductIds[] = $_product->getId();
                    }
                } else {
                    $matchedProductIds[] = $_product->getId();
                }

                if (!empty($matchedProductIds) && in_array($_product->getId(), $matchedProductIds)) {

                    $ruleCollection->addFieldToSelect('rule_id')->addStoreFilter($rule->getId());
                    $ruleCollection->addFieldToSelect('rule_id')->addCustomerGroupFilter($rule->getId());

                    if (!empty($rule->getData('store_id'))) {
                        if (in_array($this->storeManager->getStore()->getId(), $rule->getData('store_id')) || in_array("0", $rule->getData('store_id'))) {
                            if (!empty($rule->getData('customer_group_id')) && in_array($this->getCustomerGroup(), $rule->getData('customer_group_id'))) {
                                $ruleId= $rule->getId();
                                break;
                            }
                            else{
                                $ruleId=0;
                            }
                        }else{
                            $ruleId = 0;
                        }
                    }
                }
            }
        }
        return $ruleId;
    }





    /**
     * Get current customer group ID
     *
     * @return int
     */
    public function getCustomerGroup()
    {
        if ($this->customerSesion->getCustomer()->getGroupId()) {
            return $this->customerSesion->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    /**
     * @param $ruleId
     * @return string
     */
    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabelSelect($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
            return $this->model->getData('product_label_select');
        }
    }

    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabel($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
            return $this->model->getData('product_label');
        }
        }
    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabelImage($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
            return $this->model->getData('product_label_image');
        }
    }

    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabelShape($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
            return $this->model->getData('product_label_shape');
        }
    }


    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabelBackgroundColor($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
           return $this->model->getData('product_label_background_color');
        }
    }


    /**
     * @param $ruleId
     * @return string
     */
    public function getProductLabelColor($rule_id){
        if(isset($rule_id) & !empty($rule_id)){
            $this->model->load($rule_id);
            return $this->model->getData('product_label_color');
        }
    }
}
