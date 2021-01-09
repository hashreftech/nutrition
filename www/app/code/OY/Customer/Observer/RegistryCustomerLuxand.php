<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 04/12/20
 * Time: 12:14 AM
 */
namespace OY\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lentesplus\Stores\Api\Data\StoresInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class RegistryCustomerLuxand implements ObserverInterface
{
    public function __construct (
        \OY\Registry\Helper\Luxand $luxand,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Message\ManagerInterface $manager,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->luxand         = $luxand;
        $this->directoryList=$directoryList;
        $this->storeManager=$storeManager;
        $this->customerRepository=$customerRepository;
        $this->manager=$manager;
        $this->messageManager = $context->getMessageManager();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $customer = $this->customerRepository->getById($observer->getCustomer()->getId());

        if($customer->getCustomAttribute('luxand_registry') && $customer->getCustomAttribute('luxand_registry')->getValue()){

            return $this;
        }

        if(!$customer->getCustomAttribute('photo')){
            $this->messageManager->addError('Debe ingresar una imagen personal.');
            throw new NoSuchEntityException(__('Debe ingresar una imagen personal.'));
        }


        $img=$customer->getCustomAttribute('photo')->getValue();

        $imagePub ='pub/media'.$img;

        //{ name: "Denis", ci: "62996855", email: "dlespinosa365@gmail.com", id: 20}
        $nameCustomer = [];
        $nameCustomer['id']=$customer->getId();
        $nameCustomer['email']=$customer->getEmail();
        $nameCustomer['name']=$customer->getFirstname().' '.$customer->getLastname();
        $nameCustomer['ci']=$customer->getCustomAttribute('ci')->getValue();

        $name=(string)json_encode($nameCustomer);
        $registry = $this->luxand->createCustomer($name, 0, $imagePub);

        if($registry){
          $customer->setCustomAttribute('luxand_registry',1);
          $customer->setCustomAttribute('luxand_id',$registry);
          $this->customerRepository->save($customer);
        }

        return $this;
    }
}
