<?php
/**
 * AfterImportDataObserver
 *
 * @copyright Copyright © 2019 Firebear Studio. All rights reserved.
 * @author    Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Observer;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\CatalogUrlRewrite\Observer\AfterImportDataObserver as MagentoAfterImportDataObserver;
use Magento\CatalogUrlRewrite\Service\V1\StoreViewService;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\MediaStorage\Service\ImageResize;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\MergeDataProviderFactory;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use function class_exists;
use function method_exists;

/**
 * Class AfterImportDataObserver
 * @package Firebear\ImportExport\Observer
 */
class AfterImportDataObserver extends MagentoAfterImportDataObserver
{
    /**
     * @var array
     */
    protected $vitalForGenerationFields = [
        'sku',
        'url_key',
        'url_path',
        'name',
        'visibility',
        'url_key_create_redirect',
        'save_rewrites_history',
    ];
    /**
     * @var ImageResize
     */
    private $imageResize;

    /**
     * AfterImportDataObserver constructor.
     *
     * @param ProductFactory $catalogProductFactory
     * @param ObjectRegistryFactory $objectRegistryFactory
     * @param ProductUrlPathGenerator $productUrlPathGenerator
     * @param StoreViewService $storeViewService
     * @param StoreManagerInterface $storeManager
     * @param UrlPersistInterface $urlPersist
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlFinderInterface $urlFinder
     * @param MergeDataProviderFactory|null $mergeDataProviderFactory
     * @param CategoryCollectionFactory|null $categoryCollectionFactory
     */
    public function __construct(
        ProductFactory $catalogProductFactory,
        ObjectRegistryFactory $objectRegistryFactory,
        ProductUrlPathGenerator $productUrlPathGenerator,
        StoreViewService $storeViewService,
        StoreManagerInterface $storeManager,
        UrlPersistInterface $urlPersist,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        MergeDataProviderFactory $mergeDataProviderFactory = null,
        CategoryCollectionFactory $categoryCollectionFactory = null
    ) {
        parent::__construct(
            $catalogProductFactory,
            $objectRegistryFactory,
            $productUrlPathGenerator,
            $storeViewService,
            $storeManager,
            $urlPersist,
            $urlRewriteFactory,
            $urlFinder,
            $mergeDataProviderFactory,
            $categoryCollectionFactory
        );
        if (class_exists(ImageResize::class)) {
            $this->imageResize = ObjectManager::getInstance()
                ->get(ImageResize::class);
        }
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            parent::execute($observer);
            if (($this->imageResize instanceof ImageResize)
                && $products = $observer->getEvent()->getBunch()
            ) {
                $adapter = $observer->getEvent()->getAdapter();
                if (isset($adapter->getParameters()['image_resize'])
                    && $adapter->getParameters()['image_resize']
                ) {
                    foreach ($products as $rowData) {
                        $rowData = $this->getProductId($rowData);
                        /** @var Product $product */
                        $product = $this->catalogProductFactory->create();
                        $product->load($rowData['entity_id']);
                        if ($product->getMediaGalleryImages()->count()) {
                            foreach ($product->getMediaGalleryImages() as $image) {
                                $this->imageResize->resizeFromImageName($image->getFile());
                            }
                            $this->addLogWriteln(__('Resize Image for product %1', $rowData['sku']), 'info');
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->addLogWriteln($e->getMessage(), 'error');
        }
    }

    /**
     * @param array $rowData
     * @return array|null
     */
    private function getProductId(array $rowData)
    {
        $newSku = $this->import->getNewSku($rowData[ImportProduct::COL_SKU]);
        if (empty($newSku) || !isset($newSku['entity_id'])) {
            return null;
        }
        if ($this->import->getRowScope($rowData) == ImportProduct::SCOPE_STORE
            && empty($rowData[self::URL_KEY_ATTRIBUTE_CODE])) {
            return null;
        }
        $rowData['entity_id'] = $newSku['entity_id'];
        return $rowData;
    }

    /**
     * @param $debugData
     * @param null $type
     */
    private function addLogWriteln($debugData, $type = null)
    {
        if (method_exists($this->import, 'addLogWriteln')
            && method_exists($this->import, 'getOutput')
        ) {
            $this->import->addLogWriteln(
                $debugData,
                $this->import->getOutput(),
                $type
            );
        }
    }
}
