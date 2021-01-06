<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 23/11/20
 * Time: 02:26 PM
 */
namespace OY\Registry\Model;

use mysql_xdevapi\Exception;
use OY\Registry\Api\RegistryManagementInterface;

class RegistryManagement implements RegistryManagementInterface
{

    public function __construct (
        \Magento\Framework\Webapi\Rest\Request $request,
        \OY\Registry\Helper\Luxand $luxand,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \OY\Registry\Model\RegistryFactory $registryFactory,
        \OY\Registry\Api\RegistryRepositoryInterface $registryRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {

        $this->request         = $request;
        $this->luxand         = $luxand;
        $this->directoryList=$directoryList;
        $this->storeManager=$storeManager;
        $this->customerRepository=$customerRepository;
        $this->registryFactory=$registryFactory;
        $this->registryRepository=$registryRepository;
        $this->timezone=$timezone;
    }

    public function recognitionFace()
    {

        $param = $this->request->getBodyParams();

        return $this->luxand->callRecognition($param['image']);
    }

    public function createCustomer()
    {
        $param = $this->request->getBodyParams();

        if(isset($param['customer_id'])){

            $customer = $this->customerRepository->getById((int)$param['customer_id']);
        }

        $imgContent64Base = $param['image']['base64_encoded_data'];
        $imgName = $param['image']['name'];
        $imgType = $param['image']['type'];
        $media_dir = $this->directoryList->getPath('media');

        $data = 'data:'.$imgType.';base64,'.$imgContent64Base;

        file_put_contents($media_dir.'/customer/'.$imgName, base64_decode($data));

        $imagePub =$this->storeManager->getStore()->getBaseUrl().'media/'.$imgName;

        return $this->luxand->createCustomer($param['name'], $param['store'], $imagePub);
    }

    public function registryCustomer()
    {
        $param = $this->request->getBodyParams();
        if(isset($param['customer_id']))
        {

            try{

                $customer = $this->customerRepository->getById((int)$param['customer_id']);
                $fullName = $customer->getFirstname().' '.$customer->getLastname();
                //$dateTime = $this->timezone->date()->format('Y-m-d H:i:s');
                $dateTime=date("Y-m-d H:i:s");

                $registry = $this->registryFactory->create();
                $registry->setCustomerId((int)$param['customer_id']);
                $registry->setDateTime($dateTime);
                $registry->setFullname($fullName);

                $this->registryRepository->save($registry);

                return [
                    "success"=>true,
                    "msg"=>"Registro satisfactorio."
                ];

            }catch (Exception $e){
                return [
                    "success"=>false,
                    "msg"=>"El usuario no existe."
                ];
            }
        }
    }


}
