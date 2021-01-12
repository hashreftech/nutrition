<?php
namespace Sparsh\ProductLabel\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class File
 * @package Sparsh\ProductLabel\Ui\DataProvider\Product\Form\Modifier
 */
class File extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $fieldCode = 'product_label_image';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }
        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'children'  => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'elementTmpl'   => 'Sparsh_ProductLabel/elements/file',
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
