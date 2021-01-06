<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/06/19
 * Time: 03:34 PM
 */

namespace OY\Plan\Helper;

use Magento\Checkout\Exception;
use Magento\Framework\App\Helper\Context;

class Data  extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_planRepository;

    protected $_planFactory;

    public function __construct(
        Context $context,\OY\Plan\Model\Repository\PlanRepository $planRepository,
        \OY\Plan\Model\PlanFactory $planFactory,
        \OY\Plan\Model\ResourceModel\Plan\CollectionFactory $collectionPlanFactory
    )
    {
        parent::__construct($context);
        $this->_planRepository = $planRepository;
        $this->_planFactory =$planFactory;
        $this->collectionPlanFactory = $collectionPlanFactory;
    }

    public function addPlanToCustomer($customerId, $plan, $orderId=0){

        $model = $this->_planFactory->create();

        $plan = 'Semana(s)';

        if($plan->getData('planning_year')) {

            $plan = 'Año(s)';
            $countPlan=$plan->getData('planning_year');

        }else if($plan->getData('planning_month')) {

            $plan = 'Mes(es)';
            $countPlan=$plan->getData('planning_year');

        }else{
            $countPlan=$plan->getData('planning_week');
        }

        try {
            $strTo = strtotime(date('m/d/Y'));

            switch ($plan) {
                case 'Año(s)':
                    $dateTo = strtotime("+".$countPlan." year", $strTo);
                    break;
                case 'Mes(es)':
                    $dateTo = strtotime("+".$countPlan." month", $strTo);
                    break;
                default:
                    $dateTo = strtotime("+".$countPlan." week", $strTo);
            }

            $now= new \DateTime();

            $model->setCustomerId($customerId);
            $model->setStatus('Activo');
            $model->setPlan($plan);
            $model->setData('plan_count',$countPlan);
            $model->setFrom(date('m/d/Y'));
            $model->setTo(date('m/d/Y', $dateTo));
            $model->setCode(strtotime($now->format('d/m/Y H:i:s')));

            if($orderId)
              $model->setOrderId($orderId);

            $modelSaved = $this->_planRepository->save($model);
            return $modelSaved->getId();

        } catch (\Exception $e){

            return false;
        }
    }

    public function hasPlanByCustomer($custormerId){

        $collectionPlan = $this->collectionPlanFactory->create();

        $collectionPlan->addFieldToFilter('customer_id', $custormerId);

        if($collectionPlan->getSize())
            return true;

        return false;
    }

    public function getEnablePlanByCustomer($custormerId){

        $collectionPlan = $this->collectionPlanFactory->create();

        $collectionPlan->addFieldToFilter('customer_id', $custormerId)
            ->addFieldToFilter('from', array('lteq' => date("Y-m-d H:i:s")))
            ->addFieldToFilter('to', array('gteq' => date("Y-m-d H:i:s")));

        if(count($collectionPlan->getData()))
            return $collectionPlan->getFirstItem();

        return null;
    }

    public function getLastPlanByCustomer($custormerId){

        $collectionPlan = $this->collectionPlanFactory->create();

        $collectionPlan->addFieldToFilter('customer_id', $custormerId);
        $collectionPlan->setOrder('value_id','ASC');

        if(count($collectionPlan->getData()))
            return $collectionPlan->getFirstItem();

        return null;
    }


}
