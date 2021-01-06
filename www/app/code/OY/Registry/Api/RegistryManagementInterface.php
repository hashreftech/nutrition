<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/11/20
 * Time: 01:54 PM
 */
namespace OY\Registry\Api;

interface RegistryManagementInterface
{
    /**
     * POST for Registry api
     * @return string
     */
    public function recognitionFace();

    /**
     * POST for Registry api
     * @return string
     */
    public function createCustomer();

    /**
     * POST for Registry api
     * @return string
     */
    public function registryCustomer();
}
