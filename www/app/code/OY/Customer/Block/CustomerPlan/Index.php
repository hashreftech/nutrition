<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 02/08/19
 * Time: 03:40 PM
 */

namespace OY\Customer\Block\CustomerPlan;


use Magento\Framework\View\Element\Template;

class Index extends \Magento\Customer\Block\Account\Dashboard\Info
{
    public function __construct(
        Template\Context $context,
        \OY\Plan\Model\ResourceModel\Plan\CollectionFactory $collectionPlanFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    )
    {
        parent::__construct($context, $currentCustomer, $subscriberFactory, $helperView, $data);
        $this->collectionPlanFactory = $collectionPlanFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function getPlans(){

        $collection = $this->collectionPlanFactory->create();
        $collection->addFieldToFilter('customer_id',$this->customerSession->getCustomerId());

        if(count($collection->getData()))
          return $collection->getData();

        return [];
    }

    public function statusPlan($from, $to){

        $today =date("Y-m-d H:i:s");

        if(strtotime($from) <= strtotime($today) && strtotime($to) >= strtotime($today))
            return 'Activo';

        return 'Inactivo';
    }

    public function typePlan($plan){

        return $plan;
    }

    public function customerVitalicio(){

        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());

        if($customer->getCustomAttribute('plan_forever'))
            return $customer->getCustomAttribute('plan_forever')->getValue();

        return false;

        //return $customer->getData('plan_forever');
    }

    public function customerCompany(){

        $customer = $this->customerRepository->getById($this->customerSession->getCustomerId());

        if($customer->getCustomAttribute('company_id') &&
            $customer->getCustomAttribute('company_id')->getValue()){

            $product = $this->productRepository->getById($customer->getCustomAttribute('company_id')->getValue());

            return $product->getName();
        }
        return false;
    }

}
