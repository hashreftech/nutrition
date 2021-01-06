<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/06/19
 * Time: 10:28 AM
 */
namespace OY\Customer\Controller\Adminhtml\Plan;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use OY\Plan\Model\Repository\PlanRepository;
use OY\Plan\Model\PlanFactory;

class Edit extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    protected $_coreRegistry = null;

    protected $_planRepository;

    protected $planFactory;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        PlanRepository $planRepository,
        PlanFactory $planFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_planRepository = $planRepository;
        $this->planFactory = $planFactory;
    }


    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->_planRepository->getById($id);

            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This plan no longer exists.'));
                $this->_redirect('*/*/*');
                return;
            }
        } else {

            $model = $this->planFactory->create();
        }

        $this->_coreRegistry->register('current_plan', $model);

        $this->_view->loadLayout();

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Administrando Plan'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? __('Editar Plan') : __('Nueva Plan')
        );

        $breadcrumb = $id ? __('Editar Plan') : __('Nueva Plan');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
    protected function _isAllowed()
    {
        return true;
    }
}
