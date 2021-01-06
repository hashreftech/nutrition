<?php
namespace Magento\Quote\Model\PaymentMethodManagement;

/**
 * Interceptor class for @see \Magento\Quote\Model\PaymentMethodManagement
 */
class Interceptor extends \Magento\Quote\Model\PaymentMethodManagement implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Payment\Model\Checks\ZeroTotal $zeroTotalValidator, \Magento\Payment\Model\MethodList $methodList)
    {
        $this->___init();
        parent::__construct($quoteRepository, $zeroTotalValidator, $methodList);
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, \Magento\Quote\Api\Data\PaymentInterface $method)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'set');
        return $pluginInfo ? $this->___callPlugins('set', func_get_args(), $pluginInfo) : parent::set($cartId, $method);
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'get');
        return $pluginInfo ? $this->___callPlugins('get', func_get_args(), $pluginInfo) : parent::get($cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($cartId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getList');
        return $pluginInfo ? $this->___callPlugins('getList', func_get_args(), $pluginInfo) : parent::getList($cartId);
    }
}
