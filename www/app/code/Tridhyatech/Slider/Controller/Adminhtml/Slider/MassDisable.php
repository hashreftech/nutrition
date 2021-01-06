<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2019 Tridhyatech (https://www.tridhya.com/)
 * @package Tridhyatech_Slider
 */
namespace Tridhyatech\Slider\Controller\Adminhtml\Slider;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Tridhyatech\Slider\Model\ResourceModel\Grid\CollectionFactory;

class MassDisable extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDisabled = 0;
        foreach ($collection->getItems() as $record) {
            $model = $this->_objectManager->create('Tridhyatech\Slider\Model\Slider');
            $record = $model->setId($record->getSliderId());
            $record->setStatus(0);
            $record->save();
            $recordDisabled++;
        }

        $this->messageManager->addSuccess(__('A total of %1 Sliders have been disabled.', $collection->getSize()));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
