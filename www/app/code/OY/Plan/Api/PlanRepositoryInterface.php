<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 05/06/19
 * Time: 03:45 PM
 */
namespace OY\Plan\Api;

interface PlanRepositoryInterface
{

    /**
     * @param \OY\Plan\Api\Data\PlanInterface $plan
     * @return \OY\Plan\Api\Data\PlanInterface $plan
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\OY\Plan\Api\Data\PlanInterface $plan);


    /**
     * @param int $planId
     * @return \OY\Plan\Api\Data\PlanInterface $plan
     * @throws NoSuchEntityException
     */
    public function getById($plan);


    /**
     * @param \OY\Plan\Api\Data\PlanInterface $plan
     * @return bool
     */
    public function delete(\OY\Plan\Api\Data\PlanInterface $plan);


    /**
     * @param int $optionsId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @return bool
     */
    public function deleteById($planId);


    /**
     * @return \OY\Plan\Model\ResourceModel\Plan\Collection
     * @throws NoSuchEntityException
     */
    public function getAll();


}
