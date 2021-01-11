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
namespace Webkul\BookingSystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\BookingSystem\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;

class SalesQuoteItemProduct implements ObserverInterface
{
     /**
      * @var \Magento\Framework\Message\ManagerInterface
      */
    protected $_messageManager;

     /**
      * @var \Webkul\BookingSystem\Helper\Data
      */
    protected $helper;

     /**
      * @var \Webkul\BookingSystem\Model\QuoteFactory
      */
    protected $_quote;

     /**
      * @var QuoteCollection
      */
    protected $_quoteCollection;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Webkul\BookingSystem\Helper\Data $helper
     * @param \Webkul\BookingSystem\Model\QuoteFactory $quote
     * @param QuoteCollection $quoteCollection
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\BookingSystem\Helper\Data $helper,
        \Webkul\BookingSystem\Model\QuoteFactory $quote,
        QuoteCollection $quoteCollection
    ) {
        $this->_messageManager = $messageManager;
        $this->helper = $helper;
        $this->_quote = $quote;
        $this->_quoteCollection = $quoteCollection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $item = $observer->getEvent()->getQuoteItem();
            $data = $item->getBuyRequest()->getData();
            $productId = $item->getProduct()->getId();
            $itemId = (int) $item->getId();
            $bookingData = $this->helper->getDetailsByQuoteId($item->getQuoteId());

            if ($this->helper->isBookingProduct($productId) && isset($data['slot_id'])) {
                $parentId = $this->helper->getParentSlotId($productId);
                $slotId = (int) $data['slot_id'];
                $result = $this->processSlotData($data, $productId);
                if ($result['error']) {
                    $this->_messageManager->addNotice(__($result['msg']));
                } else {
                    if ($itemId > 0) {
                        $collection = $this->_quoteCollection->create();
                        $tempitem = $this->helper->getDataByField($itemId, 'item_id', $collection);

                        if (!$tempitem) {
                            $data =  [
                                'item_id' => $itemId,
                                'slot_id' => $slotId,
                                'parent_slot_id' => $parentId,
                                'quote_id' => $item->getQuoteId()
                            ];
                            $this->_quote->create()->setData($data)->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Observer_SalesQuoteItemProduct execute : ".$e->getMessage());
        }
    }

    /**
     * Process Slot Data
     *
     * @param array  $data
     * @param int    $productId
     * @param object $item
     *
     * @return array
     */
    private function processSlotData($data, $productId)
    {
        $result = ['error' => false];
        try {
            $parentId = $this->helper->getParentSlotId($productId);

            if ($parentId != $data['parent_id']) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }

            $slotId = (int) $data['slot_id'];
            if ($slotId == 0) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Observer_SalesQuoteItemProduct processSlotData : ".$e->getMessage());
        }
        return $result;
    }
}
