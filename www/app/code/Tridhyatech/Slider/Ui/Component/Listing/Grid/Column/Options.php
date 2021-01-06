<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Ui\Component\Listing\Grid\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
class Options extends Column
{
    public function __construct(
    ContextInterface $context,
    UiComponentFactory $uiComponentFactory,
    array $components = [],
    array $data = []
) {
    parent::__construct($context, $uiComponentFactory, $components, $data);
}

public function prepareDataSource(array $dataSource)
{
    if (isset($dataSource['data']['items'])) {
        foreach ($dataSource['data']['items'] as &$items) {
            if (!empty($items['cms'] || $items['products'])) {
                $m = explode(',', $items['cms']);
                $p = explode(',', $items['products']);
                $d = explode(',', $items['displayon']);
               $items['displayon'] = array_merge($d, $m ,$p);
            }
        }
    }
    return $dataSource;
}
}
