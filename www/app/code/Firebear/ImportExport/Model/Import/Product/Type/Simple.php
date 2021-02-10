<?php
/**
 * @copyright: Copyright © 2018 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Import\Product\Type;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\ImportExport\Model\Import;

/**
 * Class Downloadable
 */
class Simple extends \Magento\CatalogImportExport\Model\Import\Product\Type\Simple
{
    use \Firebear\ImportExport\Traits\Import\Product\Type;

    /**
     * AbstractType constructor
     *
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetColFac
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac
     * @param ResourceConnection $resource
     * @param array $params
     * @param MetadataPool|null $metadataPool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected $eavConfig;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetColFac,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac,
        \Magento\Framework\App\ResourceConnection $resource,
        array $params,
        MetadataPool $metadataPool = null,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($attrSetColFac, $prodAttrColFac, $resource, $params, $metadataPool);
    }
}
