<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03/12/20
 * Time: 09:53 AM
 */
namespace OY\Registry\Controller\Adminhtml\Ajax;
use Magento\Backend\App\Action;


class Registry extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \OY\Registry\Model\RegistryFactory $registryFactory,
        \OY\Registry\Api\RegistryRepositoryInterface $registryRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \OY\Plan\Model\ResourceModel\Plan\CollectionFactory $collectionPlanFactory,
        \OY\Plan\Api\PlanRepositoryInterface $planRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Webkul\BookingSystem\Model\ResourceModel\Booked\CollectionFactory $bookedCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;

        $this->customerRepository=$customerRepository;
        $this->registryFactory=$registryFactory;
        $this->registryRepository=$registryRepository;
        $this->timezone=$timezone;
        $this->collectionPlanFactory=$collectionPlanFactory;
        $this->planRepository=$planRepository;
        $this->config=$config;
        $this->bookedCollectionFactory=$bookedCollectionFactory;
        $this->customerCollectionFactory=$customerCollectionFactory;
    }


    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        $data=[];

        if($this->getRequest()->getParam('customer_id'))
        {

            try{

                if($this->isEnableReserve()){

                    $dateTimeBooked=date("d-m-Y");

                    $bookedCollection = $this->bookedCollectionFactory->create();
                    $bookedCollection->addFieldToFilter('booking_from', ['like' => '%'.$dateTimeBooked.'%']);

                    if($bookedCollection->getSize()){
                        $booked = $bookedCollection->getFirstItem();
                        $data["reserve"] = 'Reserva desde '.$booked->getData('booking_from').' hasta '.$booked->getData('booking_too');
                    }
                    else{
                        $data["reserve"] = 'No tiene reserva para hoy.';
                    }
                }

                $collection = $this->collectionPlanFactory->create();
                $collection->addFieldToFilter('customer_id',(int)$this->getRequest()->getParam('customer_id'));
                $hasPlan = false;

                if($collection->getSize()){

                    foreach ($collection as $plan){

                        if($this->statusPlan($plan->getData('from'), $plan->getData('to'))){

                           if($plan->getData('access_number')){

                               if($plan->getData('access_enabled')){

                                   $plan->setData('access_enabled', (int)$plan->getData('access_enabled')-1);
                                   $this->planRepository->save($plan);
                                   $hasPlan = true;
                               }

                           }else{
                               $hasPlan = true;
                           }

                        }
                    }
                }

                if(!$hasPlan){

                    $data["success"]=false;
                    $data["msg"]="El usuario no tiene plan asociado.";
                    return $result->setData($data);
                }

                $customer = $this->customerRepository->getById((int)$this->getRequest()->getParam('customer_id'));
                $fullName = $customer->getFirstname().' '.$customer->getLastname();
                //$dateTime = $this->timezone->date()->format('Y-m-d H:i:s');
                $dateTime=date("Y-m-d H:i:s");

                $registry = $this->registryFactory->create();
                $registry->setCustomerId((int)$this->getRequest()->getParam('customer_id'));
                $registry->setDateTime($dateTime);
                $registry->setFullname($fullName);

                $this->registryRepository->save($registry);

                $data["success"]=true;
                $data["msg"]="Registro satisfactorio.";


            }catch (Exception $e){

                $data["success"]=false;
                $data["msg"]="El usuario no existe.";
                return $result->setData($data);
            }
        }
        
        if($this->getRequest()->getParam('ci')){

          $customerCollection = $this->customerCollectionFactory->create();
            $customerCollection->addAttributeToSelect('*')
            ->addAttributeToFilter('ci',$this->getRequest()->getParam('ci'));

          if($customerCollection->getSize()){

              $customer = $this->customerRepository->getById((int)$customerCollection->getFirstItem()->getId());

              $data["success"]=true;
              $data['id']=$customer->getId();
              $data['email']=$customer->getEmail();
              $data['name']=$customer->getFirstname().' '.$customer->getLastname();
              if($customer->getCustomAttribute('ci'))
                $data['ci']=$customer->getCustomAttribute('ci')->getValue();
              if($customer->getCustomAttribute('photo'))
                $data['photo']=$customer->getCustomAttribute('photo')->getValue();

              $dateTime=date("Y-m-d H:i:s");
              $fullName = $customer->getFirstname().' '.$customer->getLastname();
              $registryCi = $this->registryFactory->create();
              $registryCi->setCustomerId((int)$customer->getId());
              $registryCi->setDateTime($dateTime);
              $registryCi->setFullname($fullName);

              $this->registryRepository->save($registryCi);
          }
        }

        return $result->setData($data);
    }

    public function statusPlan($from, $to){

        $today =date("Y-m-d H:i:s");

        if(strtotime($from) <= strtotime($today) && strtotime($to) >= strtotime($today))
            return true;

        return false;
    }

    private function getConfig($config_path)
    {
        return $this->config->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function isEnableReserve(){
        return $this->getConfig("reserve_general/config_general/enable");
    }


}

