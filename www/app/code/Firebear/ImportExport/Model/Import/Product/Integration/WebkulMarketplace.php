<?php
/**
 * WebkulMarketplace
 *
 * @copyright Copyright © 2018 Firebear Studio. All rights reserved.
 * @author    Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Import\Product\Integration;

use Exception;
use Firebear\ImportExport\Model\Import\Product;
use Webkul\Marketplace\Helper\Data;
use Webkul\Marketplace\Observer\AdminhtmlCustomerSaveAfterObserver;

/**
 * Class WebkulMarketplace
 * @package Firebear\ImportExport\Model\Import\Product\Integration
 */
class WebkulMarketplace extends AbstractIntegration
{
    const COL_UNASSIGN_SELLER = 'webkull_unassign_any_seller';
    const VENDOR_ID = 'webkull_vendor_id';

    /**
     *
     */
    public function importData($verbosity = false)
    {
        if ($verbosity) {
            $this->getOutput()->setVerbosity($verbosity);
        }
        $this->addLogWriteln(__('WebKul Marketplace Integration'), $this->getOutput());
        $this->_construct();
        try {
            /** @var \Webkul\Marketplace\Observer\AdminhtmlCustomerSaveAfterObserver $webKulProductManager */
            $webKulProductManager = $this->getObjectManager()
                ->get(AdminhtmlCustomerSaveAfterObserver::class);
            /** @var \Webkul\Marketplace\Helper\Data $webKulHelperManager */
            $webKulHelperManager = $this->getObjectManager()->get(Data::class);
            $webKulAssignData = [];
            $webKulUnAssignData = [];
            while ($bunch = $this->getDataSourceModel()->getNextBunch()) {
                foreach ($bunch as $rowData) {
                    if (isset($rowData[Product::COL_SKU], $rowData[static::VENDOR_ID])
                        && $webKulProductManager->isSeller($rowData[static::VENDOR_ID])
                    ) {
                        $productIdFromSku = (int)$this->getProductId($rowData[Product::COL_SKU]);

                        $webKulAssignData[$rowData[static::VENDOR_ID]][] = $productIdFromSku;

                        if (isset($rowData[self::COL_UNASSIGN_SELLER])) {
                            $sellerModelId = $webKulHelperManager->getSellerProductDataByProductId($productIdFromSku)
                                ->getFirstItem()->getId();
                            if ($sellerModelId) {
                                $webKulUnAssignData[$sellerModelId][] = $productIdFromSku;
                            }
                        }
                    }
                }
            }
            foreach ($webKulUnAssignData as $sellerId => $productId) {
                $this->addLogWriteln(__('Removing Products from Seller %1', $sellerId), $this->getOutput());
                $webKulProductManager->unassignProduct($sellerId, json_encode(array_flip($productId)));
            }
            foreach ($webKulAssignData as $sellerId => $productId) {
                $this->addLogWriteln(__('Adding Products to Seller %1', $sellerId), $this->getOutput());
                $webKulProductManager->assignProduct($sellerId, json_encode(array_flip($productId)));
            }
        } catch (Exception $e) {
            $this->addLogWriteln($e->getMessage(), $this->getOutput(), 'error');
        }
    }
}
