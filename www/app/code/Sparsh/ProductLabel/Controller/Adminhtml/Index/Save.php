<?php
/**
 * Sparsh ProductLabel
 * php version 7.0.31
 *
 * @category Sparsh
 * @package   Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */

namespace Sparsh\ProductLabel\Controller\Adminhtml\Index;

use Sparsh\ProductLabel\Model\ProductLabels;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Sparsh\ProductLabel\Model\ImageUploader;

/**
 * Class Save
 *
 * @category Sparsh
 * @package   Sparsh_ProductLabel
 * @author   Sparsh <magento@sparsh-technologies.com>
 * @license  https://www.sparsh-technologies.com  Open Software License (OSL 3.0)
 * @link     https://www.sparsh-technologies.com
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var ProductLabels
     */
    protected $model;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Save constructor.
     * @param Context $context
     * @param ProductLabels $model
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        ProductLabels $model,
        SerializerInterface $serializer,
        ImageUploader $imageUploader
    ) {
        $this->model = $model;
        $this->serializer = $serializer;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data = $this->prepareData($data);
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            if (isset($data['rule'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }
            $data['updation_time'] = date('Y-m-d h:i:s');
            unset($data['conditions_serialized']);
            unset($data['actions_serialized']);

            $data['condition'] = $this->serializer->serialize($data['conditions']);

            if(isset($data['product_label_image'])){
                if (isset($data['product_label_image'][0]['name']) && isset($data['product_label_image'][0]['tmp_name'])) {
                    $data['product_label_image'] = $data['product_label_image'][0]['name'];
                    $this->imageUploader->moveFileFromTmp($data['product_label_image']);
                } elseif (isset($data['product_label_image'][0]['name']) && !isset($data['product_label_image'][0]['tmp_name'])) {
                    $data['product_label_image'] = $data['product_label_image'][0]['name'];
                }
            }
            $this->model->loadPost($data);

            try {
                $this->model->setData($data)->save();
                $this->messageManager->addSuccess(__('Product label rules are saved successfully.'));
                if ($this->getRequest()->getParam('back')) {
                    if (!empty($data['rule_id'])) {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            ['id' => $data['rule_id'],
                                '_current' => true]
                        );
                    } else {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            ['_current' => true]
                        );
                    }
                }
                return $resultRedirect->setPath('*/*/');

            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the rule.'));
                $this->messageManager->addException($e, $e->getMessage());
            }

            if (!empty($data['rule_id'])) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $data['rule_id']]);
            } else {
                return $resultRedirect->setPath('*/*/edit');
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

     protected function prepareData($data)
        {
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            unset($data['rule']);
            return $data;
        }
}
