<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Controller\Adminhtml;

abstract class Slider extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Tridhyatech_Slider::slider';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
   /* public function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Tridhyatech_Slider::slider')
            ->addBreadcrumb(__('Slider'), __('Slider'))
            ->addBreadcrumb(__('Slider'), __('Slider'));
        return $resultPage;
    }*/

    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Tridhyatech_Slider::slider')
            ->_addBreadcrumb(__('Slider'), __('Slider'));
        return $this;
    }
}
