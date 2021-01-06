<?php
namespace Magento\Payment\Model\MethodList;

/**
 * Interceptor class for @see \Magento\Payment\Model\MethodList
 */
class Interceptor extends \Magento\Payment\Model\MethodList implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Payment\Helper\Data $paymentHelper, \Magento\Payment\Model\Checks\SpecificationFactory $specificationFactory, array $additionalChecks = [])
    {
        $this->___init();
        parent::__construct($paymentHelper, $specificationFactory, $additionalChecks);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableMethods(?\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAvailableMethods');
        return $pluginInfo ? $this->___callPlugins('getAvailableMethods', func_get_args(), $pluginInfo) : parent::getAvailableMethods($quote);
    }
}
