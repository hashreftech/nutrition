<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/06/19
 * Time: 12:53 PM
 */
namespace OY\Customer\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry
    )
    {
        $this->request = $context->getRequest();
        parent::__construct($context, $registry);
    }


    public function getButtonData()
    {

        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 30,
        ];
    }


}
