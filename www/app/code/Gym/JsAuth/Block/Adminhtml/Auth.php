<?php

namespace Gym\JsAuth\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Exception\NoSuchEntityException;

class Auth extends Template
{

    /**
     * Auth constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getAdminBaseUrl()
    {
        // TODO return http://magento.local.com/pub/static/version1606607469/adminhtml/Magento/backend/en_US/Gym_JsAuth/js
        // this is used to load models in face api library
        try {
            return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        } catch (NoSuchEntityException $e) {
        }
    }
    public function getApiUrl()
    {
        // TODO return luxand cloud
        // this is used to make a call to recognition api
        return 'https://api.luxand.cloud/photo/search';
    }
    public function getApiToken()
    {
        // TODO return luxand token
        // this is used to make a call to recognition api
        return '92900c4118484515ab836f0faefcf2e5';
    }

    public function getUrlRegistry()
    {
        return $this->getUrl('registry/ajax/registry', []);
    }
}
