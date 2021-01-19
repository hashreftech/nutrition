<?php

/**
 * Coderkube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Coderkube
 * @package     Coderkube_Testimonials
 * @copyright   Copyright (c) 2018 Coderkube
 */
namespace Coderkube\Testimonials\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Savetestimonial extends \Magento\Framework\App\Action\Action
{
    /** @var  \Magento\Framework\View\Result\Page */
    public $mediaDirectory;
    public $fileUploaderFactory;
    public $fileId = 'image';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;
 
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;
    public $testimonialTemplate;
    public $scopeConfigInterface;
    public $dataObjectFactory;
    public $resultPageFactory;
    public $filesystem;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Coderkube\Testimonials\Model\Testimonial $testimonialTemplate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->testimonialTemplate = $testimonialTemplate;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $this->mediaDirectory =  $this->filesystem
                                      ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $resultRedirect = $this->resultRedirectFactory->create();
        $template = $this->testimonialTemplate;
        $data = $this->getRequest()->getParams();
        $files = $this->getRequest()->getFiles('image');
        $store = $this->storeManager->getStore();
        $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $imgPath =$mediaUrl."ck/testimonial/";
        try {
             $data['image'] = $profile_img = '';
            if (isset($files) && isset($files['name']) && $files['name'] != '') {
                $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
                $img_file = $uploader->validateFile();
                if ($img_file["name"] != "") {
                    $target = $this->mediaDirectory->getAbsolutePath('ck/testimonial/');
                    $uploader = $this->fileUploaderFactory->create(['fileId' => 'image']);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'zip', 'doc']);
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($target);
                    $image=str_replace(" ", "_", $img_file["name"]);
                    $template->setData('image', $image);
                    $data['image']= $imgPath.$profile_img = $image;
                }
            }
            $template->setData('name', $this->getRequest()->getParam('name'));
            $template->setData('email', $this->getRequest()->getParam('email'));
            $template->setData('content', $this->getRequest()->getParam('message'));
            $template->setData('rate', $this->getRequest()->getParam('rate'));
            $template->setData('company_name', $this->getRequest()->getParam('cname'));
            $template->setData('website', $this->getRequest()->getParam('website'));
            $template->setData('status', "2");
            $template->save();
            $customer_email =$this->getRequest()->getParam('email');
            $admin_email = $this->scopeConfigInterface->getValue('testimonial/general/admin_email');
            
             /*If Admin not save store config once then following values will blank. so set default value.*/
            $customer_email_template = $this->scopeConfigInterface->getValue('testimonial/general/customer_template');
            if($customer_email_template==''){
                $customer_email_template = 'testimonial_general_customer_template';
            }
            $admin_email_template = $this->scopeConfigInterface->getValue('testimonial/general/admin_template');
            if($admin_email_template==''){
                $admin_email_template = 'testimonial_general_admin_template';
            }
            $senderdata =$this->scopeConfigInterface->getValue('testimonial/general/email_sender');
            
            if($senderdata!=''){
                $senderName =$this->scopeConfigInterface->getValue('trans_email/ident_'.$senderdata.'/name');
                $senderEmail =$this->scopeConfigInterface->getValue('trans_email/ident_'.$senderdata.'/email');
            }else{
                $senderName =$this->scopeConfigInterface->getValue('trans_email/ident_general/name');
                $senderEmail =$this->scopeConfigInterface->getValue('trans_email/ident_general/email');
            }
           
            $sender = [
                'name' => $senderName,
                'email' => $senderEmail,
            ];
           
            $postObject = $this->dataObjectFactory->create();
            $postObject->setData($data);
            
            $storeId =1;
            $templateParams = [];
            
            

            /**admin email start***/
            
            if($admin_email!=''){
                $this->inlineTranslation->suspend();
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $transport = $this->transportBuilder
                   ->setTemplateIdentifier($admin_email_template)
                   ->setTemplateOptions(
                       [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ]
                   )
                   ->setTemplateVars(['data' => $postObject])
                   ->setFrom($sender)
                   ->addTo($admin_email)
                   ->getTransport();
                 $transport->sendMessage();
                 $this->inlineTranslation->resume();
            }
             /**admin email end***/ 

             /**customer email start***/
             if(!empty($customer_email)){
                 
                 $this->inlineTranslation->suspend();
             $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
             $transport = $this->transportBuilder
               ->setTemplateIdentifier($customer_email_template)
               ->setTemplateOptions(
                   [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
               )
               ->setTemplateVars(['data' => $postObject])
               ->setFrom($sender)
               ->addTo($customer_email)
               ->getTransport();
             }
             
             $transport->sendMessage();
             $this->inlineTranslation->resume();
             /**customer email end***/
             $this->messageManager->addSuccess(__('Testimonial has been successfully added.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath(
                'testimonial/index/newtestimonial',
                ['_secure'=>$this->getRequest()->isSecure()]
            );
        }
        return $this->resultRedirectFactory->create()->setPath(
            'testimonial',
            ['_secure'=>$this->getRequest()->isSecure()]
        );
    }
}
