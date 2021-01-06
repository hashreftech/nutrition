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
namespace Webkul\BookingSystem\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutFactory;

class BookingOptions extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    public function __construct(
        LocatorInterface $locator,
        RequestInterface $request,
        LayoutFactory $layoutFactory
    ) {
        $this->locator = $locator;
        $this->request = $request;
        $this->layoutFactory = $layoutFactory;
    }

    public function modifyMeta(array $meta)
    {
        if ($this->getProductType() == "booking") {
            $meta["booking_tab"] = [
                "children" => [
                    "booking_tab_container" => [
                        "arguments" => [
                            "data" => [
                                "config" => [
                                    "formElement" => "container",
                                    "componentType" => "container",
                                    'component' => 'Magento_Ui/js/form/components/html',
                                    "label" => __("Booking Information"),
                                    "required" => 0,
                                    "sortOrder" => 1,
                                    "content" => $this->layoutFactory->create()->createBlock(
                                        \Webkul\BookingSystem\Block\Adminhtml\Catalog\Product\Edit\Tab\Booking::class
                                    )->toHtml(),
                                ]
                            ]
                        ]
                    ]
                ],
                "arguments" => [
                    "data" => [
                        "config" => [
                            "componentType" => "fieldset",
                            "label" => __("Booking Information"),
                            "collapsible" => true,
                            "sortOrder" => 1,
                            'opened' => true,
                            'canShow' => true
                        ]
                    ]
                ]
            ];
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Get product type
     *
     * @return null|string
     */
    private function getProductType()
    {
        return (string)$this->request->getParam('type', $this->locator->getProduct()->getTypeId());
    }
}
