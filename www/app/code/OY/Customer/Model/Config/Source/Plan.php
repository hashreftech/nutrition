<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/12/20
 * Time: 04:16 PM
 */
namespace OY\Customer\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Galgo\Company\Model\ResourceModel\Company\CollectionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Custom Attribute Renderer
 *
 * @author      Webkul Core Team <support@webkul.com>
 */
class Plan extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        return array(
            array('value'=>'Semanal', 'label'=>'Semanal'),
            array('value'=>'Mensual', 'label'=>'Mensual'),
            array('value'=>'Anual', 'label'=>'Anual')
        );
    }
}
