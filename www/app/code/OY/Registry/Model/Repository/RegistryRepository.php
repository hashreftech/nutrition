<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace OY\Registry\Model\Repository;

use OY\Registry\Api\Data;
use OY\Registry\Api\RegistryRepositoryInterface;
use OY\Registry\Model\ResourceModel\Registry as ResourceRate;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;


class RegistryRepository implements RegistryRepositoryInterface
{

    protected $_resourceRegistry;

    protected $_registryFactory;

    protected $registryCollectionFactory;

    public function __construct(
        ResourceRate $resource,
        \OY\Registry\Model\RegistryFactory $registryFactory,
        \OY\Registry\Model\ResourceModel\Registry\CollectionFactory $registryCollectionFactory
    ) {
        $this->_resourceRegistry = $resource;
        $this->_registryFactory = $registryFactory;
        $this->registryCollectionFactory = $registryCollectionFactory;
    }

    public function save(\OY\Registry\Api\Data\RegistryInterface $registry)
    {
        try {
            $this->_resourceRegistry->save($registry);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $registry;
    }


    public function getById($id)
    {
        $registry = $this->_registryFactory->create();
        $this->_resourceRegistry->load($registry, $id);
        if (!$registry->getId()) {
            throw new NoSuchEntityException(__('Ip with id "%1" does not exist.', $id));
        }
        return $registry;
    }

    public function delete(Data\RegistryInterface $registry)
    {
        try {
            $this->_resourceRegistry->delete($registry);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

}
