<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06/12/20
 * Time: 12:57 PM
 */
namespace OY\Customer\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\Exception\NoSuchEntityException;

class AjaxPhoto extends Template
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

    public function getUrlPhoto()
    {
        return $this->getUrl('customer/ajax/photo', []);
    }
}
