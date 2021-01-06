<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/12/20
 * Time: 02:07 PM
 */
namespace OY\Sales\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
class OrderSaveAfter implements ObserverInterface {
    public function __construct(
        \OY\Plan\Model\Repository\PlanRepository $planRepository,
        \OY\Plan\Model\PlanFactory $planFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->planRepository=$planRepository;
        $this->planFactory=$planFactory;
        $this->dateFilter = $dateFilter;
        $this->productRepository=$productRepository;
    }


    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $orderStatus       = $order->getStatus();

        if($customerId){ // Order Criada

            if($orderStatus == Order::STATE_COMPLETE){

                foreach ($order->getAllVisibleItems() as $orderItem) {
                    $options = $orderItem->getProductOptions();

                    if(isset($options['info_buyRequest']) && isset($options['info_buyRequest']['options'])){

                        foreach ($options['info_buyRequest']['options'] as $opt){

                            if(isset($opt['date'])){

                                $product = $this->productRepository->getById($orderItem->getProductId());
                                $model = $this->planFactory->create();

                                if($product->getData('planning_type')){

                                    try {

                                        //$splitDate = explode('/',$opt['date']);
                                        $dateFormatNew = $opt['date'];//$splitDate[1].'/'.$splitDate[0].'/'.$splitDate[2];

                                        $strTo =$this->dateFilter->filter($dateFormatNew);

                                        $typePlan = $product->getData('planning_type');

                                        $count = $orderItem->getQtyOrdered();

                                        $plan = $typePlan;


                                        switch ($typePlan) {
                                            case 'Anual':
                                                //$dateTo = date("+".$data['plan_year']." years", $strTo);
                                                $dateTo=date("Y-m-d H:i:s", strtotime("+".$count." years", strtotime($strTo)));
                                                break;
                                            case 'Mensual':
                                                //$dateTo = date("+".$data['plan_month']." months", $strTo);
                                                $dateTo=date("Y-m-d H:i:s", strtotime("+".$count." months", strtotime($strTo)));
                                                break;
                                            default:
                                                //$dateTo = date("+".$count." weeks", $strTo);
                                                $weeks = $count*7;
                                                $dateTo=date("Y-m-d H:i:s", strtotime("+".$weeks." days", strtotime($strTo)));
                                        }

                                        $model->setData('customer_id',$customerId);
                                        $model->setData('plan',$plan);

                                        $model->setData('from',date("Y-m-d H:i:s", strtotime("+3 hours", strtotime($strTo))));

                                        $model->setData('to',$dateTo);

                                        $model->setData('access_number',0);
                                        $model->setData('access_enabled',0);
                                        if($product->getData('number_access')){

                                            $model->setData('access_number',$product->getData('number_access'));
                                            $model->setData('access_enabled',$product->getData('number_access'));
                                        }

                                        $this->planRepository->save($model);


                                    } catch (LocalizedException $e) {

                                    }
                                }


                            }

                        }
                    }

                }
            }

        }
    }
}
