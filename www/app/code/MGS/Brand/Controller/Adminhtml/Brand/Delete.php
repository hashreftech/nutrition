<?php

namespace MGS\Brand\Controller\Adminhtml\Brand;

class Delete extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('brand_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create('MGS\Brand\Model\Brand');
                $model->load($id);

				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

				$eavAttribute = $objectManager->create('Magento\Eav\Model\Config');

				$attribute = $eavAttribute->getAttribute('catalog_product', 'mgs_brand');

				$options = $attribute->getSource()->getAllOptions();
				$options['value'][$model->getOptionId()] = true;
				$options['delete'][$model->getOptionId()] = true;

				$setupObject = $objectManager->create('Magento\Eav\Setup\EavSetup');
				$setupObject->addAttributeOption($options);
				
				
                $model->delete();
                $this->messageManager->addSuccess(__('The brand has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a brand to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MGS_Brand::delete_brand');
    }
}
