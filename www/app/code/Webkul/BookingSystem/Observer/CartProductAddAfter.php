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
use Magento\Framework\App\RequestInterface;
use Webkul\BookingSystem\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;

class CartProductAddAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Webkul\BookingSystem\Helper\Data
     */
    private $bookingHelper;

    /**
     * @var \Webkul\BookingSystem\Model\QuoteFactory
     */
    private $quote;

    /**
     * @var QuoteCollection
     */
    private $quoteCollection;

    /**
     * @param RequestInterface                            $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Webkul\BookingSystem\Helper\Data           $bookingHelper
     * @param \Webkul\BookingSystem\Model\QuoteFactory    $quote
     * @param QuoteCollection                             $quoteCollection
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\BookingSystem\Helper\Data $bookingHelper,
        \Webkul\BookingSystem\Model\QuoteFactory $quote,
        QuoteCollection $quoteCollection
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->bookingHelper = $bookingHelper;
        $this->quote = $quote;
        $this->quoteCollection = $quoteCollection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->request->getParams();
        $helper = $this->bookingHelper;
        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');
        $productId = $product->getId();
        $itemId = (int) $item->getId();
        if ($helper->isBookingProduct($productId)) {
            $parentId = $helper->getParentSlotId($productId);
            if (empty($data['slot_id'])) {
                $itemData = $item->getBuyRequest()->getData();
                $slotId = (int) $itemData['slot_id'];
            } else {
                $slotId = (int) $data['slot_id'];
            }
            $result = $this->processSlotData($data, $productId, $item);
            if ($result['error']) {
                throw new \Magento\Framework\Exception\LocalizedException($result['msg']);
            }
            try {
                if ($itemId > 0) {
                    $collection = $this->quoteCollection->create();
                    $item = $helper->getDataByField($itemId, 'item_id', $collection);
                    if ($item) {
                        $id = $item->getId();
                        $data =  [
                            'item_id' => $itemId,
                            'slot_id' => $slotId,
                            'parent_slot_id' => $parentId,
                            'quote_id' => $item->getQuoteId()
                        ];
                        $this->quote->create()->addData($data)->setId($id)->save();
                    }
                }
            } catch (\Exception $e) {
                $this->bookingHelper->logDataInLogger(
                    "Observer_CartProductAddAfter_execute Exception : ".$e->getMessage()
                );
            }
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
    private function processSlotData($data, $productId, $item)
    {
        $result = ['error' => false];
        try {
            $helper = $this->bookingHelper;
            $parentId = $helper->getParentSlotId($productId);
            if ($parentId != $data['parent_id']) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }

            $slotId = (int) $data['slot_id'];
            if ($slotId == 0) {
                $msg = __('There was some error while processing your request');
                $result = ['error' => true, 'msg' => $msg];
            }

            $slotData = $helper->getSlotData($slotId, $parentId, $productId);
            $availableQty = $slotData['qty'];
            $requestedQty = $item->getQty();
            if ($requestedQty > $availableQty) {
                $msg = __('Slot quantity not available');
                $result = ['error' => true, 'msg' => $msg];
            }
        } catch (\Exception $e) {
            $this->bookingHelper->logDataInLogger(
                "Observer_CartProductAddAfter_processSlotData Exception : ".$e->getMessage()
            );
        }
        return $result;
    }
}
