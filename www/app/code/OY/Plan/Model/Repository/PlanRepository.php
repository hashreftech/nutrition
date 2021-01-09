<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/06/19
 * Time: 04:49 PM
 */
namespace OY\Plan\Model\Repository;

use OY\Plan\Api\Data;
use OY\Plan\Api\PlanRepositoryInterface;
use OY\Plan\Model\ResourceModel\Plan as Plan;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;


class PlanRepository implements PlanRepositoryInterface
{

    protected $_resourcePlan;

    protected $_planFactory;

    protected $planCollectionFactory;

    public function __construct(
        Plan $resource,
        \OY\Plan\Model\PlanFactory $planFactory,
        \OY\Plan\Model\ResourceModel\Plan\CollectionFactory $planCollectionFactory
    ) {
        $this->_resourcePlan = $resource;
        $this->_planFactory = $planFactory;
        $this->planCollectionFactory = $planCollectionFactory;
    }

    public function save(\OY\Plan\Api\Data\PlanInterface $plan)
    {
        try {
            $this->_resourcePlan->save($plan);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $plan;
    }

    public function getById($planId)
    {
        $model = $this->_planFactory->create();
        $this->_resourcePlan->load($model, $planId);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Info plan with id "%1" does not exist.', $planId));
        }
        return $model;
    }

    public function delete(Data\PlanInterface $model)
    {
        try {
            $this->_resourcePlan->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($planId)
    {
        return $this->delete($this->getById($planId));
    }

    public function getAll(){

        return $this->planCollectionFactory->create();
    }


}
