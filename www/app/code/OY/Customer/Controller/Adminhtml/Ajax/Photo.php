<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 03/12/20
 * Time: 09:53 AM
 */
namespace OY\Customer\Controller\Adminhtml\Ajax;
use Magento\Backend\App\Action;


class Photo extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    )
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->directoryList=$directoryList;
    }


    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data=[];

        if($this->getRequest()->getParam('photo')){

            $mediaDir = $this->directoryList->getPath('media');

            if (!is_dir($mediaDir.'/customer/luxand')) {
                mkdir($mediaDir.'/customer/luxand', 0777, true);
            }

            if($this->getRequest()->getParam('current')){
                unlink($mediaDir.$this->getRequest()->getParam('current'));
            }

            $imgBase64 = $this->getRequest()->getParam('photo');
            $photoId = rand(100000000, 999999999);
            $imgName='img'.$photoId.'.png';

            file_put_contents($mediaDir.'/customer/luxand/'.$imgName, base64_decode($imgBase64));

            $data["path_photo"]= '/customer/luxand/'.$imgName;

        }

        return $result->setData($data);
    }


}

