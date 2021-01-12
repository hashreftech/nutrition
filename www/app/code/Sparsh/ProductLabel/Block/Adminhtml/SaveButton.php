<?php
/**
 * Sparsh ProductLabel Module
 * php version 7.0.31
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */

namespace Sparsh\ProductLabel\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 *
 * @category Sparsh
 * @package  Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Save Button
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('sparsh_product_label/index/save')),
            'data_attribute' => [
                'mage-init' => ['button' => ['product_label' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
