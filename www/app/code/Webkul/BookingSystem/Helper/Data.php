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
namespace Webkul\BookingSystem\Helper;

use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Webkul\BookingSystem\Model\ResourceModel\Info\CollectionFactory as InfoCollection;
use Webkul\BookingSystem\Model\ResourceModel\Slot\CollectionFactory as SlotCollection;
use Webkul\BookingSystem\Model\ResourceModel\Booked\CollectionFactory as BookedCollection;
use Webkul\BookingSystem\Model\ResourceModel\Quote\CollectionFactory as QuoteCollection;
use Webkul\BookingSystem\Model\OptionMapFactory;
use Magento\Catalog\Model\Product;
use Webkul\BookingSystem\Model\ResourceModel\CleanupReservationData;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_option;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var InfoCollection
     */
    protected $_infoCollection;

    /**
     * @var SlotCollection
     */
    protected $_slotCollection;

    /**
     * @var BookedCollection
     */
    protected $_bookedCollection;

    /**
     * @var QuoteCollection
     */
    protected $_quoteCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Webkul\BookingSystem\Logger\Logger
     */
    private $logger;
    
    /**
     * @var Webkul\BookingSystem\Model\OptionMapFactory
     */
    private $optionMapFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var array
     */
    public $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    /**
     * @param \Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Framework\Message\ManagerInterface  $messageManager
     * @param \Magento\Framework\Data\Form\FormKey         $formKey
     * @param \Magento\Catalog\Model\ProductFactory        $productFactory
     * @param \Magento\Sales\Model\OrderFactory            $order
     * @param \Magento\Checkout\Model\Cart                 $cart
     * @param \Magento\Catalog\Model\Product\OptionFactory $option
     * @param ProductCollection                            $productCollectionFactory
     * @param InfoCollection                               $infoCollectionFactory
     * @param SlotCollection                               $slotCollectionFactory
     * @param BookedCollection                             $bookedCollectionFactory
     * @param QuoteCollection                              $quoteCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\BookingSystem\Model\ProductFactory $bookingProductFactory,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product\OptionFactory $option,
        ProductCollection $productCollectionFactory,
        InfoCollection $infoCollectionFactory,
        SlotCollection $slotCollectionFactory,
        BookedCollection $bookedCollectionFactory,
        QuoteCollection $quoteCollectionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Webkul\BookingSystem\Logger\Logger $logger,
        OptionMapFactory $optionMapFactory,
        CleanupReservationData $cleanupReservation,
        SerializerInterface $serializer,
        CartHelper $cartHelper,
        WishlistHelper $wishlistHelper
    ) {
        $this->_request = $context->getRequest();
        $this->_messageManager = $messageManager;
        $this->_formKey = $formKey;
        $this->_product = $productFactory;
        $this->_bookingProduct = $bookingProductFactory;
        $this->_order = $order;
        $this->_cart = $cart;
        $this->_option = $option;
        $this->_productCollection = $productCollectionFactory;
        $this->_infoCollection = $infoCollectionFactory;
        $this->_slotCollection = $slotCollectionFactory;
        $this->_bookedCollection = $bookedCollectionFactory;
        $this->_quoteCollection = $quoteCollectionFactory;
        $this->_stockRegistry = $stockRegistry;
        $this->timezone = $timezoneInterface;
        $this->logger = $logger;
        $this->optionMapFactory = $optionMapFactory;
        $this->cleanupReservation = $cleanupReservation;
        $this->serializer = $serializer;
        $this->cartHelper = $cartHelper;
        $this->wishlistHelper = $wishlistHelper;
        parent::__construct($context);
    }

    /**
     * Get Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        try {
            return $this->_formKey->getFormKey();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getFormKey : ".$e->getMessage());
        }
    }

    /**
     * Get Cart
     *
     * @return object
     */
    public function getCart()
    {
        try {
            $cartModel = $this->_cart;
            return $cartModel;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCart : ".$e->getMessage());
        }
    }

    /**
     * @return Magento\Checkout\Helper\Cart
     */
    public function getCartHelper()
    {
        return $this->cartHelper;
    }

    /**
     * @return Magento\Wishlist\Helper\Data
     */
    public function getWishListHelper()
    {
        return $this->wishlistHelper;
    }

    public function getSerializedString($array)
    {
        try {
            return $this->serializer->serialize($array);
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getSerializedString : ".$e->getMessage());
        }
    }

    /**
     * Get Current Product Id
     *
     * @param int $type [optional]
     *
     * @return int
     */
    public function getProductId($type = 0)
    {
        try {
            $id = (int) $this->_request->getParam('id');
            if ($type > 1) {
                $id = (int) $this->_request->getParam('product_id');
            }

            return $id;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getProductId : ".$e->getMessage());
        }
    }

    /**
     * Get All Parameters
     *
     * @return int
     */
    public function getParams()
    {
        try {
            return $this->_request->getParams();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getParams : ".$e->getMessage());
        }
    }

    /**
     * Get Product
     *
     * @param int $productId [optional]
     *
     * @return object
     */
    public function getProduct($productId = 0)
    {
        try {
            if ($productId == 0) {
                $productId = $this->getProductId();
            }
            $product = $this->_product->create()->load($productId);
            return $product;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getProduct : ".$e->getMessage());
        }
    }

    /**
     * Check Slot Quantities are Available or Not
     */
    public function checkStatus()
    {
        try {
            $cartModel = $this->getCart();
            $quote = $cartModel->getQuote();
            $flag = false;
            foreach ($quote->getAllVisibleItems() as $item) {
                $flag = $this->processItem($item);
            }
            if ($flag) {
                $this->getCart()->save();
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data checkStatus : ".$e->getMessage());
        }
    }

    /**
     * Process Quoirte Item
     *
     * @param object $item
     */
    public function processItem($item)
    {
        try {
            $productId = $item->getProductId();
            if ($this->isBookingProduct($productId)) {
                $itemId = $item->getId();
                $requestedQty = $item->getQty();
                $data = $this->getAvailableSlotQty($productId, $itemId);
                if ($data && !empty($data) && isset($data['qty'])) {
                    $qty = $data['qty'];
                    $flag = $this->isBookingExpired($data);
                    if ($flag) {
                        $this->_messageManager->addError(__('Booking Time Expired'));
                        $this->getCart()->removeItem($itemId)->save();
                        return true;
                    }
                }
                if ($requestedQty > $qty) {
                    $item->setQty($qty);
                    $this->_messageManager->addError(__('Slot quantity not available'));
                }

                if ($qty <= 0) {
                    $this->getCart()->removeItem($itemId)->save();
                    return true;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data processItem : ".$e->getMessage());
        }
    }

    public function isBookingExpired($data)
    {
        try {
            if ($this->getCurrentTime(true) >= strtotime($data['booking_from'])) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data isBookingExpired : ".$e->getMessage());
        }
    }

    /**
     * Get Full Action Name
     *
     * @return string
     */
    public function getFullActionName()
    {
        try {
            return $this->_request->getFullActionName();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getFullActionName : ".$e->getMessage());
        }
    }

    /**
     * Check Whether It is Product Page or Not
     *
     * @return boolean
     */
    public function isBookingProductPage()
    {
        try {
            if ($this->getFullActionName() == 'catalog_product_view') {
                $productId = $this->_request->getParam('id');
                if ($this->isBookingProduct($productId)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data isBookingProductPage : ".$e->getMessage());
            return false;
        }
    }

    /**
     * Check Whether Product is Booking Type or Not
     *
     * @param int     $productId
     * @param boolean $useCollection [optional]
     *
     * @return boolean
     */
    public function isBookingProduct($productId, $useCollection = false)
    {
        try {
            $isProduct =  false;
            if ($useCollection) {
                $collection = $this->_productCollection->create();
                $collection->addFieldToFilter('entity_id', $productId);
                foreach ($collection as $item) {
                    $isProduct = true;
                    $product =  $item;
                    break;
                }

                if (!$isProduct) {
                    return false;
                }
            } else {
                $product = $this->getProduct($productId);
            }

            $productType = $product->getTypeId();
            if ($productType == 'booking') {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data isBookingProduct : ".$e->getMessage());
            return false;
        }
    }

    /**
     * Check Cart Configure is Allowed or Not
     *
     * @return boolean
     */
    public function canConfigureCart()
    {
        try {
            $productId = (int)$this->_request->getParam('product_id');
            $product = $this->getProduct($productId);
            $productType = $product->getTypeId();
            if ($productType == 'booking') {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data canConfigureCart : ".$e->getMessage());
            return true;
        }
    }

    /**
     * Get Product's Options
     *
     * @param int $productId [optional]
     *
     * @return json
     */
    public function getProductOptions($productId = '')
    {
        try {
            $array = [];
            $product = $this->getProduct($productId);
            $mappedOptions = $this->getMappedOption($product->getId());
            foreach ($product->getOptions() as $option) {
                $optionId = $option->getId();
                if ($mappedOptions && $mappedOptions->getFrom() == $optionId) {
                    $optionTitle = 'Booking From';
                } elseif ($mappedOptions && $mappedOptions->getTill() == $optionId) {
                    $optionTitle = 'Booking Till';
                } else {
                    $optionTitle = $option->getTitle();
                }
                $array[] = ['id' => $optionId, 'title' => $optionTitle];
            }
            
            return $array;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getProductOptions : ".$e->getMessage());
            return [];
        }
    }

    /**
     * Get Booking Type
     *
     * @param int $productId
     *
     * @return int
     */
    public function getBookingType($productId)
    {
        try {
            $bookingInfo = $this->getBookingInfo($productId);
            return $bookingInfo['type'];
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getBookingType : ".$e->getMessage());
        }
    }

    /**
     * Get Dropdown For Day Select
     *
     * @param string $name  [optional]
     * @param string $value [optional]
     *
     * @return html
     */
    public function getDaySelectHtml($name = '', $value = '')
    {
        try {
            $htmlClass = "admin__control-select wk-day-select";
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $html = "";
            $html .= "<select data-form-part='product_form' class='".$htmlClass."' name='".$name."'>";
            foreach ($days as $key => $day) {
                if ($value == strtolower($day)) {
                    $html .= "<option selected value='".strtolower($day)."'>".__($day)."</option>";
                } else {
                    $html .= "<option value='".strtolower($day)."'>".__($day)."</option>";
                }
            }

            $html .= "</select>";
            return $html;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDaySelectHtml : ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get Slots of Booking Product
     *
     * @param int $productId
     * @param int $parentId  [optional]
     *
     * @return array
     */
    public function getSlots($productId, $parentId = 0)
    {
        try {
            $info = [];
            $bookingInfo = $this->getBookingInfo($productId);
            $result = $this->prepareOptions($bookingInfo, $bookingInfo['type']);
            if (!empty($result)) {
                $info = $result['slots'];
            }

            return $info;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getSlots : ".$e->getMessage());
            return [];
        }
    }

    /**
     * Get Parent Slot Id of Slot
     *
     * @param int $productId
     *
     * @return int
     */
    public function getParentSlotId($productId)
    {
        try {
            $id = 0;
            $collection = $this->_slotCollection
                ->create()
                ->addFieldToFilter("product_id", $productId)
                ->addFieldToFilter("status", 1);
            if ($collection->getSize()) {
                foreach ($collection as $item) {
                    $id = $item->getId();
                    break;
                }
            }
            return $id;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getParentSlotId : ".$e->getMessage());
            return 0;
        }
    }

    /**
     * Format Slot Data
     *
     * @param array $slot
     * @param int   $count
     * @param array $bookedSlots [optional]
     *
     * @return array
     */
    public function formatSlot($slot, $bookedSlots = [])
    {
        try {
            $format = "d-m-Y";
            $id = $slot['id'];
            $startTime = $slot['startTime'];
            $endTime = $slot['endTime'];
            $qty = $slot['qty'];
            $bookedQty = 0;
            if (!empty($bookedSlots)) {
                if ($bookedSlots['is_booked']) {
                    if (array_key_exists($id, $bookedSlots)) {
                        $bookedQty = $bookedSlots[$id];
                    }
                }
            }

            $qty = $qty - $bookedQty;
            $qtyInfo = "";
            if ($qty > 1) {
                $qtyInfo = $qty." slots available";
            } elseif ($qty == 1) {
                $qtyInfo = $qty." slot available";
            }

            $startTime = $this->convertTimeFromSeconds($startTime);

            $endTime = $this->convertTimeFromSeconds($endTime);
            $from = $slot['date']." ".$startTime;
            $strtotimeFrom = strtotime($from);
            $from = date($format.",h:i a", $strtotimeFrom);
            $to = $slot['date']." ".$endTime;
            $to = date($format.",h:i a", strtotime($to));
            $slotTime = $startTime." - ".$endTime;

            $info = [
                'id' => $id,
                'slot' => $slotTime,
                'qty' => $qty,
                'day' => __(ucfirst($slot['day'])),
                'month' => __(date("F", strtotime($slot['date']))),
                'year' => date("Y", strtotime($slot['date'])),
                'day1' => ucfirst(substr($slot['day'], 0, 3)),
                'date' => date($format, strtotime($slot['date'])),
                'date_formatted' => date("j,F Y", strtotime($slot['date'])),
                'booking_from' => $from,
                'booking_to' => $to
            ];

            if (isset($slot['end_day'])) {
                $strToTimeStart = strtotime($startTime);
                $strToTimeEnd = strtotime($endTime);

                for ($i = 0; $i < 7; $i++) {
                    $weekOfTheDay = strftime("%A", strtotime("last ".$slot['day']." +$i day"));
                    $weekDayNames[] = strtolower($weekOfTheDay);
                }

                if ($slot['day']==$slot['end_day']) {
                    if ($strToTimeStart < $strToTimeEnd) {
                        $endDate = strtotime($slot['date']." ".$endTime);
                        $formated_from_date_raw = date($format.",h:i a", $endDate);
                        if ($endDate) {
                            $info['booking_to'] = $formated_from_date_raw;
                            $info['no_of_days'] = "01";
                        }
                    } else {
                        $tempString = "+7 days ";
                        $endDate = strtotime($tempString, strtotime($slot['date']." ".$endTime));
                        $formated_from_date_raw = date($format.",h:i a", $endDate);
                        if ($endDate) {
                            $info['booking_to'] = $formated_from_date_raw;
                            $info['no_of_days'] = "07";
                        }
                    }
                } else {
                    $dayKey = array_search($slot['day'], $weekDayNames);
                    $endDayKey = array_search($slot['end_day'], $weekDayNames);
                    $noOfDays = $endDayKey - $dayKey;
                    if ($noOfDays > 0) {
                        if ($noOfDays == 1) {
                            $tempString = "+".$noOfDays." days ";
                        } elseif ($noOfDays > 1) {
                            $tempString = "+".$noOfDays." day ";
                        }

                        $endDate = strtotime($tempString, strtotime($slot['date']." ".$endTime));
                        $formated_from_date_raw = date($format.",h:i a", $endDate);
                        if ($endDate) {
                            $info['booking_to'] = $formated_from_date_raw;
                            $noOfDays = ($noOfDays < 10) ? "0".$noOfDays : $noOfDays;
                            $info['no_of_days'] = $noOfDays;
                        }
                    }
                }
            }

            return $info;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data formatSlot : ".$e->getMessage());
        }
    }

    public function getCurrentTimeZone()
    {
        try {
            $tz = $this->timezone->getConfigTimezone();
            date_default_timezone_set($tz);
            return $tz;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentTimeZone : ".$e->getMessage());
        }
    }

    /**
     * Get Arranged Slots Data of Booking Product
     *
     * @param int $productId
     * @param int $parentId  [optional]
     *
     * @return array
     */
    public function getFormattedSlots($productId, $parentId = 0)
    {
        try {
            $info = [];
            $count = 1;
            if ($parentId == 0) {
                $parentId = $this->getParentSlotId($productId);
                $bookingSlots = $this->getSlots($productId);
            } else {
                $bookingSlots = $this->getSlots($productId, $parentId);
            }

            $bookedSlots = $this->getBookedSlotsQty($parentId);

            foreach ($bookingSlots as $date => $slots) {
                foreach ($slots as $slot) {
                    $slot['date'] = $date;
                    $info[$count] = $this->formatSlot($slot, $bookedSlots);
                    $count++;
                }
            }

            return $info;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getFormattedSlots : ".$e->getMessage());
            return [];
        }
    }

    /**
     * Get Single Slot Data
     *
     * @param int $slotId
     * @param int $parentId
     * @param int $productId
     *
     * @return array
     */
    public function getSlotData($slotId, $parentId, $productId)
    {
        try {
            $slotData = [];
            $slots = $this->getFormattedSlots($productId, $parentId);
            if (array_key_exists($slotId, $slots)) {
                $slotData = $slots[$slotId];
            }

            return $slotData;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getSlotData : ".$e->getMessage());
            return [];
        }
    }

    /**
     * Get Booking Info
     *
     * @param int $productId
     *
     * @return array
     */
    public function getBookingInfo($productId)
    {
        $bookingInfo = ['is_booking' => false, 'type' => 0];
        try {
            $collection = $this->_infoCollection
                ->create()
                ->addFieldToFilter("product_id", $productId);
            if ($collection->getSize()) {
                foreach ($collection as $item) {
                    $bookingInfo = $item->getData();
                    $info = $item->getInfo();
                    $info = $this->serializer->unserialize($info);
                    $bookingInfo['info'] = $info;
                    $bookingInfo['is_booking'] = true;
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getBookingInfo : ".$e->getMessage());
        }
        return $bookingInfo;
    }

    /**
     * Get Booked Slots Quantity
     *
     * @param int $parentSlotId
     *
     * @return array
     */
    public function getBookedSlotsQty($parentSlotId)
    {
        $bookedInfo = ['is_booked' => false];
        try {
            $collection = $this->_bookedCollection
                ->create()
                ->addFieldToFilter("parent_slot_id", $parentSlotId);
            if ($collection->getSize()) {
                foreach ($collection as $item) {
                    $slotId = $item->getSlotId();
                    $qty = $item->getQty();
                    if (array_key_exists($slotId, $bookedInfo)) {
                        $bookedInfo[$slotId] = $bookedInfo[$slotId] + $qty;
                    } else {
                        $bookedInfo[$slotId] = $qty;
                    }

                    $bookedInfo['is_booked'] = true;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getBookedSlotsQty : ".$e->getMessage());
        }
        return $bookedInfo;
    }

    /**
     * Disable Old Slots
     *
     * @param int $productId
     */
    public function disableSlots($productId)
    {
        try {
            $collection = $this->_slotCollection
                ->create()
                ->addFieldToFilter("product_id", $productId);
            if ($collection->getSize()) {
                foreach ($collection as $slot) {
                    $this->disableSlot($slot);
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data disableSlots : ".$e->getMessage());
        }
    }

    /**
     * Disable Slot
     *
     * @param object $slot
     */
    public function disableSlot($slot)
    {
        try {
            $slot->addData(['status' => 0])
                ->setId($slot->getId())
                ->save();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data disableSlot : ".$e->getMessage());
        }
    }

    /**
     * Get Total Booking Quantity
     *
     * @param int $productId
     *
     * @return int
     */
    public function getTotalBookingQty($productId)
    {
        try {
            $bookingInfo = $this->getBookingInfo($productId);
            if (!$bookingInfo['is_booking']) {
                return 0;
            }

            $qty = $bookingInfo['qty'];
            $totalSlots = $bookingInfo['total_slots'];

            if ($this->getFullActionName() == "catalog_product_edit" ||
                $this->getFullActionName() == "checkout_onepage_success" ||
                $this->getFullActionName() == "sales_order_invoice_save"
            ) {
                return $qty;
            } else {
                return $qty*$totalSlots;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getTotalBookingQty : ".$e->getMessage());
        }
    }

    /**
     * Enable Booking Custom Option on Product
     *
     * @param int $productId
     */
    public function enableOptions($productId)
    {
        try {
            if ($this->isBookingProduct($productId)) {
                $product = $this->getProduct($productId);
                $this->manageBookingOption($product);
                $this->updateProduct($productId);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data enableOptions : ".$e->getMessage());
        }
    }

    /**
     * Update Product Options
     *
     * @param int $productId
     */
    public function updateProduct($productId)
    {
        try {
            $data = ['has_options' => 1, 'required_options' => 1];
            $product = $this->_bookingProduct->create()->load($productId);
            $product->addData($data)->setId($productId)->save();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data updateProduct : ".$e->getMessage());
        }
    }

    /**
     * Get Added Slot Details
     *
     * @param int $itemId
     *
     * @return array
     */
    public function getDetailsByQuoteItemId($itemId)
    {
        $info = ['error' => true];
        try {
            if ($itemId && $itemId!=="") {
                $collection = $this->_quoteCollection
                    ->create()
                    ->addFieldToFilter("item_id", $itemId);
                foreach ($collection as $item) {
                    $info = $item->getData();
                    $info['error'] = false;
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDetailsByQuoteItemId : ".$e->getMessage());
        }
        return $info;
    }

    /**
     * Get Added Slot Details
     *
     * @param int $itemId
     *
     * @return array
     */
    public function getDetailsByQuoteId($quoteId)
    {
        $info = ['error' => true];
        try {
            $collection = $this->_quoteCollection
                ->create()
                ->addFieldToFilter("quote_id", $quoteId);
            foreach ($collection as $item) {
                $info = $item->getData();
                $info['error'] = false;
                break;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDetailsByQuoteId : ".$e->getMessage());
        }
        return $info;
    }

    /**
     * Get Available Slot Quantity
     *
     * @param int $productId
     * @param int $itemId
     *
     * @return int
     */
    public function getAvailableSlotQty($productId, $itemId)
    {
        try {
            $info = $this->getDetailsByQuoteItemId($itemId);
            if (!$info['error']) {
                $data = $this->getSlotData($info['slot_id'], $info['parent_slot_id'], $productId);
                return $data;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getAvailableSlotQty : ".$e->getMessage());
        }
        return false;
    }

    /**
     * Get Added Quantity of Slot in Cart
     *
     * @param int $slotId
     * @param int $parentId
     *
     * @return int
     */
    public function inCartSlotQty($slotId, $parentId)
    {
        $itemId = 0;
        $qty = 0;
        try {
            $collection = $this->_quoteCollection
                ->create()
                ->addFieldToFilter("slot_id", $slotId)
                ->addFieldToFilter("parent_slot_id", $parentId);
            foreach ($collection as $item) {
                $itemId = $item->getItemId();
                break;
            }

            $cartModel = $this->getCart();
            $quote = $cartModel->getQuote();
            foreach ($quote->getAllVisibleItems() as $item) {
                if ($itemId == $item->getId()) {
                    $qty = $item->getQty();
                    break;
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data inCartSlotQty : ".$e->getMessage());
        }
        return $qty;
    }

    /**
     * Get Order Object
     *
     * @param int $orderId
     *
     * @return object
     */
    public function getOrder($orderId)
    {
        try {
            $order = $this->_order->create()->load($orderId);
            return $order;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getOrder : ".$e->getMessage());
        }
    }

    /**
     * Get Formatted Date
     *
     * @param string $format
     * @param int    $timestamp [optional]
     *
     * @return string
     */
    public function formatDate($format, $timestamp = 0)
    {
        try {
            return date($format, $timestamp);
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data formatDate : ".$e->getMessage());
        }
    }

    /**
     * Get Days in Month
     *
     * @param string $month [optional]
     * @param string $year  [optional]
     *
     * @return int
     */
    public function daysInMonth($month = '', $year = '')
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            $date = $year.'-'.$month.'-01';

            return date('t', strtotime($date));
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data daysInMonth : ".$e->getMessage());
        }
    }

    /**
     * Get Month Title
     *
     * @param string $month [optional]
     * @param string $year  [optional]
     *
     * @return string
     */
    public function getMonth($month = '', $year = '')
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            $date = $year.'-'.$month.'-01';

            return date('F', strtotime($date));
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getMonth : ".$e->getMessage());
        }
    }

    /**
     * Get Index of Start Day
     *
     * @param string $month [optional]
     * @param string $year  [optional]
     *
     * @return int
     */
    public function getStartDay($month = '', $year = '')
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            $date = $year.'-'.$month.'-01';

            return date('N', strtotime($date));
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getStartDay : ".$e->getMessage());
        }
    }

    /**
     * Get Number of Weeks in Month
     *
     * @param string $month [optional]
     * @param string $year  [optional]
     *
     * @return int
     */
    public function weeksInMonth($month = '', $year = '')
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            $daysInMonths = $this->daysInMonth($month, $year);
            $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + (int)($daysInMonths / 7);
            $monthEndingDay = date('N', strtotime($year.'-'.$month.'-'.$daysInMonths));
            $monthStartDay = date('N', strtotime($year.'-'.$month.'-01'));
            if ($monthEndingDay < $monthStartDay) {
                ++$numOfweeks;
            }

            return $numOfweeks;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data weeksInMonth : ".$e->getMessage());
        }
    }

    /**
     * Get Calendar Title Html Block
     *
     * @param string  $month     [optional]
     * @param string  $year      [optional]
     * @param int     $productId [optional]
     * @param boolean $prev      [optional]
     * @param boolean $next      [optional]
     *
     * @return html
     */
    public function getCalendarTitle($month = '', $year = '', $productId = 0, $prev = false, $next = false)
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            $html = "<div class='wk-calendar-title wk-title-'".$productId.">";
            if ($prev) {
                $html .= "<span class='wk-previous-cal'></span>";
            }

            $html .= __($this->getMonth($month, $year)).' '.$year;
            if ($next) {
                $html .= "<span class='wk-next-cal'></span>";
            }

            $html .= '</div>';

            return $html;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCalendarTitle : ".$e->getMessage());
        }
    }

    /**
     * Get Calendar Header Html Block
     *
     * @return html
     */
    public function getCalendarHeader()
    {
        try {
            $html = "<div class='wk-calendar-head'>";
            foreach ($this->dayLabels as $label) {
                $html .= "<div class='wk-calendar-col' title='".__($label)."'>".__($label).'</div>';
            }

            $html .= '</div>';
            return $html;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCalendarHeader : ".$e->getMessage());
        }
    }

    /**
     * Validate Value
     *
     * @param string $value
     * @param string $defaultValue
     *
     * @return string
     */
    public function validateEntry($value, $defaultValue)
    {
        try {
            if ($value == '') {
                return $defaultValue;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data validateEntry : ".$e->getMessage());
        }
        return $value;
    }

    /**
     * Get Month Value
     *
     * @param int $month
     *
     * @return int
     */
    public function getMonthValue($month)
    {
        try {
            if ($month > 12) {
                $month = 1;
            }
            return $month;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getMonthValue : ".$e->getMessage());
        }
    }

    /**
     * Get Indexed Month Value
     *
     * @param int $month
     *
     * @return int | string
     */
    public function getIndexedMonth($month)
    {
        try {
            if ($month < 10) {
                $month = '0'.$month;
            }
            return $month;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getIndexedMonth : ".$e->getMessage());
        }
    }

    /**
     * Get Indexed Day Value
     *
     * @param int $day
     *
     * @return int | string
     */
    public function getIndexedDay($day)
    {
        try {
            if ($day < 10) {
                $day = '0'.$day;
            }
            return $day;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getIndexedDay : ".$e->getMessage());
        }
    }

    public function getCurrentTime($isDate = false)
    {
        try {
            // Date for a specific date/time:
            $date = new \DateTime();

            // Convert timezone
            $tz = new \DateTimeZone($this->getCurrentTimeZone());
            $date->setTimeZone($tz);

            // Output date after
            if ($isDate) {
                return strtotime($date->format('Y-m-d H:i:s'));
            } else {
                return strtotime($date->format('H:i:s'));
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentTime : ".$e->getMessage());
        }
    }

    /**
     * Get Day Class
     *
     * @param int $day
     * @param int $month
     * @param int $year
     *
     * @return string
     */
    public function getDayClass($day, $month, $year)
    {
        $dayClass = '';
        try {
            $currentDay = $this->getCurrentDay();
            $currentMonth = $this->getCurrentMonth();
            $currentYear = $this->getCurrentYear();

            if ($year < $currentYear) {
                $dayClass = 'wk-passed-day';
            } elseif ($year == $currentYear) {
                if ($month < $currentMonth) {
                    $dayClass = 'wk-passed-day';
                } elseif ($month == $currentMonth) {
                    if ($day < $currentDay) {
                        $dayClass = 'wk-passed-day';
                    } else {
                        $dayClass = 'wk-available-day';
                    }
                } else {
                    $dayClass = 'wk-available-day';
                }
            } else {
                $dayClass = 'wk-available-day';
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDayClass : ".$e->getMessage());
        }
        return $dayClass;
    }

    /**
     * Create Html of Calendar
     *
     * @param string  $month     [optional]
     * @param string  $year      [optional]
     * @param int     $productId [optional]
     * @param boolean $prev      [optional]
     * @param boolean $next      [optional]
     * @param string  $class     [optional]
     *
     * @return html
     */
    public function createCalendar($month = '', $year = '', $productId = 0, $prev = false, $next = false, $class = '')
    {
        try {
            $month = $this->validateEntry($month, $this->getCurrentMonth());
            $year = $this->validateEntry($year, $this->getCurrentYear());
            $month = $this->getMonthValue($month);
            $html = "<div class='wk-calendar-conatiner ".$class."'>";
            $html .= $this->getCalendarTitle($month, $year, $productId, $prev, $next);
            $html .= "<div class='wk-calendar-content'>";
            $html .= $this->getCalendarHeader();
            $html .= "<div class='wk-calendar-body'>";
            $weeksInMonth = $this->weeksInMonth($month, $year);
            $daysInMonth = $this->daysInMonth($month, $year);
            $month = $this->getIndexedMonth($month);
            $k = 0;
            $bookingClass = '';

            $defaultClass = "wk-calendar-cell wk-day ";
            $parentId = $this->getParentSlotId($productId);
            $slots = $this->getSlots($productId);

            $bookedSlots = $this->getBookedSlotsQty($parentId);
            for ($i = 0; $i < $weeksInMonth; ++$i) {
                $html .= "<div class='wk-calendar-row'>";
                for ($j = 1; $j <= 7; ++$j) {
                    $day = $i * 7 + $j;
                    $startDay = $this->getStartDay($month, $year);
                    if ($day >= $startDay && $k < $daysInMonth) {
                        ++$k;
                        $dateDay = $this->getIndexedDay($k);
                        $date = $year."-".$month."-".$dateDay;
                        $dayClass = $this->getDayClass($k, $month, $year);
                        $html .= "<div class='wk-calendar-col'>";

                        $bookingClass = "slot-not-available";
                        $allBooked = "";

                        if (array_key_exists($date, $slots) && $dayClass=='wk-available-day') {
                            $info = $slots[$date];
                            $allBooked = $this->getAllSlotsBookedClass($info, $bookedSlots);
                            $notAvailable = $this->getBookingClass($info);
                            $bookingClass = (!empty($info)) ? "slot-available" : $bookingClass;
                            
                            $bookingClass = (strtotime($this->getCurrentDate()) == strtotime($date)
                                && $bookingClass == "slot-available"
                                && $notAvailable
                                )
                                ? "slot-not-available"
                                : $bookingClass;
                        }
                        $divClass = $defaultClass.$bookingClass.$allBooked;
                        $html .= "<div data-date='".$date."' class='".$divClass.' '.$dayClass."'>";
                        $html .= $k;
                        $html .= '</div>';
                        $html .= '</div>';
                    } else {
                        $html .= "<div class='wk-calendar-col'></div>";
                    }
                }

                $html .= '</div>';
            }

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            return $html;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data createCalendar : ".$e->getMessage());
        }
    }

    /**
     * @param array $info
     * @return boolean
     */
    private function getBookingClass($info)
    {
        $notAvailable = true;
        $currentTime = $this->getCurrentTime();
        foreach ($info as $key => $value) {
            $startTime = strtotime($this->convertTimeFromSeconds($value['startTime']));
            if ($startTime > $currentTime) {
                $notAvailable = false;
                break;
            }
        }

        return $notAvailable;
    }

    public function convertTimeFromSeconds($seconds)
    {
        try {
            $hour = floor($seconds/60);
            $minute = floor($seconds%60);
            if ($minute <= 9) {
                $minute = "0".$minute;
            }
            $time = $hour.":".$minute;
            return $time;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data convertTimeFromSeconds : ".$e->getMessage());
        }
    }

    private function getAllSlotsBookedClass($info, $bookedSlots)
    {
        try {
            $allBooked = true;
            if ($bookedSlots['is_booked']==1) {
                foreach ($info as $key => $value) {
                    if (array_key_exists($value['id'], $bookedSlots)) {
                        $actualQty = $value['qty'] - $bookedSlots[$value['id']];
                        if ($actualQty > 0) {
                            $allBooked = false;
                            break;
                        }
                    } else {
                        $allBooked = false;
                        break;
                    }
                }
            } else {
                $allBooked = false;
            }

            if ($allBooked) {
                return " booked-slot";
            } else {
                return "";
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getAllSlotsBookedClass : ".$e->getMessage());
            return "";
        }
    }

    /**
     * Get Current Date
     *
     * @return string
     */
    public function getCurrentDate()
    {
        try {
            return date('Y-m-d');
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentDate : ".$e->getMessage());
        }
    }

    /**
     * Get Current Day
     *
     * @return int
     */
    public function getCurrentDay()
    {
        try {
            return date('d');
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentDay : ".$e->getMessage());
        }
    }

    /**
     * Get Current Month
     *
     * @return int
     */
    public function getCurrentMonth()
    {
        try {
            return date('m');
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentMonth : ".$e->getMessage());
        }
    }

    /**
     * Get Current Year
     *
     * @return int
     */
    public function getCurrentYear()
    {
        try {
            return date('Y');
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCurrentYear : ".$e->getMessage());
        }
    }

    /**
     * Get Calendar Html
     *
     * @param string  $month     [optional]
     * @param string  $year      [optional]
     * @param int     $productId [optional]
     * @param boolean $prev      [optional]
     * @param boolean $next      [optional]
     * @param string  $class     [optional]
     *
     * @return html
     */
    public function getCalendar($month = '', $year = '', $productId = 0, $prev = false, $next = false, $class = '')
    {
        try {
            if ($month == '') {
                $month = $this->getCurrentMonth();
            }

            if ($year == '') {
                $year = $this->getCurrentYear();
            }

            return $this->createCalendar($month, $year, $productId, $prev, $next, $class);
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getCalendar : ".$e->getMessage());
        }
    }

    /**
     * Get Calendar for Booking Product
     *
     * @param int $productId
     *
     * @return html
     */
    public function getAllCalendars($productId)
    {
        try {
            $bookingInfo = $this->getBookingInfo($productId);
            if ($bookingInfo['is_booking']) {
                $startDate = $bookingInfo['start_date'];
                $endDate = $bookingInfo['end_date'];
            } else {
                $startDate = "";
                $endDate = "";
            }

            $html = "";
            $startMonth = (int) date('m', strtotime($startDate));
            $startYear = (int) date('Y', strtotime($startDate));
            $endMonth = (int) date('m', strtotime($endDate));
            $endYear = (int) date('Y', strtotime($endDate));
            $arr = [];
            $diff = $endYear - $startYear;
            if ($diff > 0) {
                $total = 12*$diff;
                $total = $total + $endMonth;
                $count = 0;
                $totalMonths = $total - $startMonth;
                $year = $startYear;
                for ($i = $startMonth; $i <= $total; $i++) {
                    $month = $i%12;
                    $month = $this->resetMonth($month);
                    $prev = $this->isPrevAllowed($count, $totalMonths);
                    $next = $this->isNextAllowed($count, $totalMonths);
                    $class = ($count == 0) ? 'wk-current-month' : '';
                    $html .= $this->getCalendar($month, $year, $productId, $prev, $next, $class);
                    $year = $this->resetYear($month, $year);
                    $count++;
                }
            } else {
                $count = 0;
                $totalMonths = $endMonth - $startMonth;
                for ($month = $startMonth; $month <= $endMonth; $month++) {
                    $prev = $this->isPrevAllowed($count, $totalMonths, 1);
                    $next = $this->isNextAllowed($count, $totalMonths, 1);
                    $class = ($count == 0) ? 'wk-current-month' : '';
                    $html .= $this->getCalendar($month, $endYear, $productId, $prev, $next, $class);
                    $count++;
                }
            }

            return $html;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getAllCalendars : ".$e->getMessage());
            return "";
        }
    }

    /**
     * Check Whether Previous Button Allowed or Not
     *
     * @param int $count
     * @param int $totalMonths
     * @param int $type[Optional]
     *
     * @return bool
     */
    public function isPrevAllowed($count, $totalMonths, $type = 0)
    {
        try {
            if ($type == 1) {
                if ($totalMonths == 0) {
                    return false;
                }
            }

            if ($count > 0) {
                return true;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data isPrevAllowed : ".$e->getMessage());
        }
        return false;
    }

    /**
     * Check Whether Next Button Allowed or Not
     *
     * @param int $count
     * @param int $totalMonths
     * @param int $type[Optional]
     *
     * @return bool
     */
    public function isNextAllowed($count, $totalMonths, $type = 0)
    {
        try {
            if ($type == 1) {
                if ($totalMonths == 0) {
                    return false;
                }
            }

            if ($count == $totalMonths) {
                return false;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data isNextAllowed : ".$e->getMessage());
        }
        return true;
    }

    /**
     * Reset Month Value
     *
     * @param int $month
     *
     * @return int
     */
    public function resetMonth($month)
    {
        try {
            if ($month == 0) {
                $month = 12;
            }

            return $month;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data resetMonth : ".$e->getMessage());
        }
    }

    /**
     * Reset Year Value
     *
     * @param int $month
     * @param int $year
     *
     * @return int
     */
    public function resetYear($month, $year)
    {
        try {
            if ($month == 12) {
                $year++;
            }

            return $year;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data resetYear : ".$e->getMessage());
        }
    }

    /**
     * Delete Booking Option
     *
     * @param object $option
     */
    public function deleteOption($option)
    {
        try {
            $mappedOption = $this->getMappedOption($option->getProductId());
            $optionId = $option->getId();
            if ($mappedOption && ($mappedOption->getFrom() == $optionId || $mappedOption->getTill() == $optionId)) {
                return $option->getId();
            } else {
                $option->delete();
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data deleteOption : ".$e->getMessage());
        }
    }

    /**
     * Mange Booking Custom Options
     *
     * @param object $product
     */
    public function manageBookingOption($product)
    {
        try {
            $optionTitles = [];
            foreach ($product->getOptions() as $option) {
                $optionTitles[] = $this->deleteOption($option);
            }
            $mappedOption = $this->getMappedOption($product->getId());
            if ($mappedOption && count($product->getOptions()) == 0) {
                $mappedOption->delete();
                $mappedOption = null;
            }

            // Creating Custom Options
            $options = [];
            $titleFrom = "Booking From";
            $titleTill = "Booking Till";

            if (!$mappedOption) {
                $options[] = [
                    'sort_order' => 1,
                    'title' => $titleFrom,
                    'price_type' => 'fixed',
                    'price' => '',
                    'type' => 'field',
                    'is_require' => 1,
                ];
                $options[] = [
                    'sort_order' => 2,
                    'title' => $titleTill,
                    'price_type' => 'fixed',
                    'price' => '',
                    'type' => 'field',
                    'is_require' => 1,
                ];
            }

            $createdOptions = [];
            foreach ($options as $arrayOption) {
                $createdOptions[] = $this->createOption($arrayOption, $product);
            }
            
            if (!empty($createdOptions)) {
                $this->mapOptions($product->getId(), $createdOptions);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data manageBookingOption : ".$e->getMessage());
        }
    }

    public function createOption($arrayOption, $product)
    {
        try {
            $product->setHasOptions(1);
            $product->getResource()->save($product);
            $option = $this->_option
                ->create()
                ->setProductId($product->getId())
                ->setStoreId($product->getStoreId())
                ->addData($arrayOption);
            $option->save();
            $product->addOption($option);
            if ($option->getId()) {
                return $option->getId();
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data createOption : ".$e->getMessage());
        }
    }

    /**
     * Get First Object From Collection
     *
     * @param array | int | string $value
     * @param array | string       $field
     * @param object               $collection
     *
     * @return $object
     */
    public function getDataByField($values, $fields, $collection)
    {
        $item = false;
        try {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $field = $fields[$key];
                    $collection = $collection->addFieldToFilter($field, $value);
                }
            } else {
                $collection = $collection->addFieldToFilter($fields, $values);
            }

            foreach ($collection as $item) {
                return $item;
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDataByField : ".$e->getMessage());
        }
        return $item;
    }

    public function checkBookingProduct($productId)
    {
        try {
            if ($this->isBookingProduct($productId)) {
                $slots = $this->getSlots($productId);

                $count = count($slots);
                if ($count <= 0) {
                    $this->setOutOfStock($productId);
                } else {
                    $qty = $this->getTotalBookingQty($productId);
                    $this->setInStock($productId, $qty);
                    $product = $this->getProduct($productId);
                    
                    if (!empty($product->getSku())) {
                        //$this->cleanupReservation->execute($product->getSku());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data checkBookingProduct : ".$e->getMessage());
        }
    }

    public function setInStock($productId, $qty)
    {
        try {
            $stockItem = $this->_stockRegistry->getStockItem($productId);
            $stockItem->setData('is_in_stock', 1);
            $stockItem->setData('qty', $qty);
            $stockItem->save();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data setInStock : ".$e->getMessage());
        }
    }

    public function setOutOfStock($productId)
    {
        try {
            $stockItem = $this->_stockRegistry->getStockItem($productId);
            $stockItem->setData('is_in_stock', 0);
            $stockItem->setData('qty', 0);
            $stockItem->save();
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data setOutOfStock : ".$e->getMessage());
        }
    }

    public function deleteInfo($productId)
    {
        try {
            $collection = $this->_infoCollection
                ->create()
                ->addFieldToFilter("product_id", $productId);
            foreach ($collection as $item) {
                $item->delete();
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data deleteInfo : ".$e->getMessage());
        }
    }

    /**
     * Prepare Options
     *
     * @param array $data
     * @param int   $bookingType
     *
     * @return array
     */
    public function prepareOptions($data, $bookingType)
    {
        $result = [];
        try {
            if ($bookingType == 1) {
                $result = $this->prepareManyBookingOptions($data);
            } elseif ($bookingType == 2) {
                $result = $this->prepareOneBookingOptions($data);
            }
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data prepareOptions : ".$e->getMessage());
        }
        return $result;
    }

    /**
     * Prepare Many Booking Options
     *
     * @param array $data
     *
     * @return array
     */
    public function prepareManyBookingOptions($data)
    {
        try {
            $slots = [];
            $count = 1;
            $info = $data['info'];
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $slotTime = $data['info']['time_slot'];
            $breakTime = $data['info']['break_time'];
            $qty = $data['qty'];
            $numOfDays = $this->getDateDifference($startDate, $endDate);
            for ($i = 0; $i <= $numOfDays; $i++) {
                $date = strtotime("+$i day", strtotime($startDate));
                $day = strtolower(date("l", $date));
                $date = strtolower(date("Y-m-d", $date));
                $status = $info[$day]['status'];
                if ($status == 1) {
                    $startHour = $info[$day]['start_hour'];
                    $startMinute = $info[$day]['start_minute'];
                    $endHour = $info[$day]['end_hour'];
                    $endMinute = $info[$day]['end_minute'];
                    $startCount = $startHour*60 + $startMinute;
                    $endCount = $endHour*60 + $endMinute;
                    $st = $startCount;
                    $diff = $endCount - $startCount;
                    while ($diff >= $slotTime) {
                        $slots[$date][] = [
                            'startTime' => $st,
                            'endTime' => $st + $slotTime,
                            'qty' => $qty,
                            'id' => $count,
                            'day' => $day
                        ];
                        $st = $st+$slotTime+$breakTime;
                        $diff = $diff - ($breakTime + $slotTime);
                        $count++;
                    }
                }
            }

            $bookingInfo = $data['info'];
            $bookingInfo['time_slot'] = $slotTime;
            $bookingInfo['break_time'] = $breakTime;
            $result = [];
            $result['info'] = $bookingInfo;
            $result['slots'] = $slots;
            $result['start_date'] = $startDate;
            $result['end_date'] = $endDate;
            $result['total'] = $count-1;
            return $result;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data prepareManyBookingOptions : ".$e->getMessage());
        }
    }

    /**
     * Prepare One Booking Options
     *
     * @param array $data
     *
     * @return array
     */
    public function prepareOneBookingOptions($data)
    {
        try {
            $slots = [];
            $count = 1;
            $info = $data['info'];
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];
            $qty = $data['qty'];
            $numOfDays = $this->getDateDifference($startDate, $endDate);
            $startData = $data['info']['start'];
            $endData = $data['info']['end'];
            $startDays = $startData['day'];
            $startHours = $startData['hour'];
            $startMinutes = $startData['minute'];
            $endDays = $endData['day'];
            $endHours = $endData['hour'];
            $endMinutes = $endData['minute'];
            for ($i = 0; $i <= $numOfDays; $i++) {
                $date = strtotime("+$i day", strtotime($startDate));
                $day = strtolower(date("l", $date));
                $date = strtolower(date("Y-m-d", $date));
                foreach ($startDays as $key => $startDay) {
                    if ($day == $startDay) {
                        $st = $startHours[$key]*60 + $startMinutes[$key];
                        $et = $endHours[$key]*60 + $endMinutes[$key];
                        $slots[$date][] = [
                            'startTime' => $st,
                            'endTime' => $et,
                            'qty' => $qty,
                            'id' => $count,
                            'day' => $day,
                            'end_day' => $endDays[$key]
                        ];
                        $count++;
                    }
                }
            }

            $bookingInfo = ['start' => $startData, 'end' => $endData];
            $result = [];
            $result['info'] = $bookingInfo;
            $result['slots'] = $slots;
            $result['total'] = $count-1;
            return $result;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data prepareOneBookingOptions : ".$e->getMessage());
        }
    }

    /**
     * Get Difference  of Dates
     *
     * @param string $firstDate
     * @param string $lastDate
     *
     * @return int
     */
    public function getDateDifference($firstDate, $lastDate)
    {
        try {
            $date1 = date_create($firstDate);
            $date2 = date_create($lastDate);
            $diff = date_diff($date1, $date2);
            $numOfDays = (int)$diff->format("%R%a");
            return $numOfDays;
        } catch (\Exception $e) {
            $this->logDataInLogger("Helper_Data getDateDifference : ".$e->getMessage());
        }
    }

    public function logDataInLogger($data)
    {
        $this->logger->info($data);
    }

    /**
     * Delete Booking Mapped Options
     *
     * @param object $options
     */
    public function getMappedOption($productId)
    {
        $collection = $this->optionMapFactory->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId]);
        if (!empty($collection)) {
            foreach ($collection as $data) {
                return $data;
            }
        }
    }

    /**
     * Map Booking Options
     *
     * @param object $options
     */
    public function mapOptions($productId, $options)
    {
        $optionMap = $this->optionMapFactory->create();
        $optionMap->setProductId($productId);
        $optionMap->setFrom($options[0]);
        $optionMap->setTill($options[1]);
        $optionMap->save();
    }

    /**
     * @return object
     */
    public function getRequestObject()
    {
        return $this->_request;
    }
}
