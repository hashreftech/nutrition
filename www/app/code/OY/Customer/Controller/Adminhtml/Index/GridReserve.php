<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/12/20
 * Time: 09:36 AM
 */
namespace OY\Customer\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class Grid
 * @package Magestore\PurchaseOrderSuccess\Controller\Adminhtml\PurchaseOrder\Product
 */
class GridReserve extends \Magento\Backend\App\Action
{

    const BLOCK_GRID = 'OY\Customer\Block\Adminhtml\Edit\Tab\ReserveGrid';
    const BLOCK_GRID_NAME = 'adminhtml.customer.edit.tab.reserve';

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
        $customerId = $this->getRequest()->getParam('id');

        if ($customerId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
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
