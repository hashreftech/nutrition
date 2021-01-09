<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace OY\Plan\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Grid
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 */
class Grid extends \Magento\Backend\App\Action
{

    const BLOCK_GRID = 'OY\Plan\Block\Adminhtml\Plan\PlanProducts';
    const BLOCK_GRID_NAME = 'adminhtml.plan.list';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    protected $_coreRegistry;



    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $coreRegistry;
    }


    /**
     * Save product to purchase order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $planProductId = $this->getRequest()->getParam('id');

        if ($planProductId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CARDPRODUCT_ID, $planProductId);
        }

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                static::BLOCK_GRID,
                static::BLOCK_GRID_NAME
            )->toHtml()
        );
    }
}
