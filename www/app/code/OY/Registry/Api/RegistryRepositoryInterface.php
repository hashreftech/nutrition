<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 01/12/20
 * Time: 12:44 PM
 */

namespace OY\Registry\Api;

interface RegistryRepositoryInterface
{

    /**
     * @param \OY\Registry\Api\Data\RegistryInterface $registry
     * @return \OY\Registry\Api\Data\RegistryInterface int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\OY\Registry\Api\Data\RegistryInterface $registry);


    /**
     * @api
     * @param int $id
     * @return \OY\Registry\Api\Data\RegistryInterface $registry
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @api
     * @param \OY\Registry\Api\Data\RegistryInterface $registry
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\OY\Registry\Api\Data\RegistryInterface $registry);

    /**
     * @api
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);

}

