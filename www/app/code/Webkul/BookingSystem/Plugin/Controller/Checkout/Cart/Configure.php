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
namespace Webkul\BookingSystem\Plugin\Controller\Checkout\Cart;

use Magento\Framework\Controller\ResultFactory;

class Configure
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    protected $_bookingHelper;

    /**
     * @param ResultFactory                               $resultFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Webkul\BookingSystem\Helper\Data           $bookingHelper
     */
    public function __construct(
        ResultFactory $resultFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\BookingSystem\Helper\Data $bookingHelper
    ) {
        $this->_resultFactory = $resultFactory;
        $this->_messageManager = $messageManager;
        $this->_bookingHelper = $bookingHelper;
    }

    public function afterExecute(\Magento\Checkout\Controller\Cart\Configure $subject, $result)
    {
        try {
            if ($this->_bookingHelper->canConfigureCart()) {
                $this->_messageManager->addError(__("Can not configure booking."));
                return $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout/cart');
            }
        } catch (\Exception $e) {
            $this->_bookingHelper
                ->logDataInLogger("Plugin_Controller_Checkout_Cart_Configure afterExecute : ".$e->getMessage());
        }
        return $result;
    }
}
