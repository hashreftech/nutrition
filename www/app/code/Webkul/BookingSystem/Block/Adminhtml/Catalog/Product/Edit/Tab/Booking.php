<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_BookingSystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\BookingSystem\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget;
use Magento\Framework\Registry;
use Webkul\BookingSystem\Helper\Data;

class Booking extends Widget
{
    /**
     * Reference to product objects that is being edited
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product = null;

    /**
     * @var string
     */
    protected $_template = 'product/edit/booking.phtml';

    /**
     * Accordion block id
     *
     * @var string
     */
    protected $_blockId = 'bookingInfo';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context  $context
     * @param Registry $registry
     * @param Data $helper
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
    
    /**
     * @return object
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setData('opened', true);
        return parent::_prepareLayout();
    }
}
