<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/06/19
 * Time: 11:22 AM
 */
namespace OY\Customer\Ui\DataProvider;


class PlanDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

    protected $_coreRegistry;


    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->_coreRegistry = $registry;
    }

    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $this->_loadedData = [];

        $cardCurrent = $this->_coreRegistry->registry('current_plan');

        if($cardCurrent && $cardCurrent->getId()) {

            $data = $cardCurrent->getData();

            /*switch ($data['plan']) {
                case 'Año(s)':
                    $data['plan_year'] = $data['plan_count'];
                    break;
                case 'Mens(es)':
                    $data['plan_month'] = $data['plan_count'];
                    break;
                default:
                    $data['plan_week'] = $data['plan_count'];
            }*/

            $this->_loadedData[$cardCurrent->getId()]=$data;

        }

        return $this->_loadedData;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }
}
