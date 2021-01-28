<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Source\Type\File\Config;

use Magento\Framework\Config\ConverterInterface;
use Firebear\ImportExport\Helper\Spout as Helper;

/**
 * Config converter
 */
class Converter implements ConverterInterface
{
    /**
     * Spout helper
     *
     * @var \Firebear\ImportExport\Helper\Spout
     */
    protected $_helper;

    /**
     * Initialize converter
     *
     * @param ManagerInterface $messageManager
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     */
    public function convert($source)
    {
        $result = [];
        /** @var \DOMNode $templateNode */
        foreach ($source->documentElement->childNodes as $typeNode) {
            if ($typeNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $typeName = $typeNode->attributes->getNamedItem('name')->nodeValue;
            if ($this->_helper->isAllowName($typeName)) {
                $typeLabel = $typeNode->attributes->getNamedItem('label')->nodeValue;
                $typeModel = $typeNode->attributes->getNamedItem('model')->nodeValue;
                $direction = $typeNode->attributes->getNamedItem('direction')->nodeValue;
                $result[$direction][$typeName] = [
                    'label' => $typeLabel,
                    'model' => $typeModel
                ];
            }
        }
        return $result;
    }
}
