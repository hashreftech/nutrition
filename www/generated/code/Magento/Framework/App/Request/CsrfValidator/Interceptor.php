<?php
namespace Magento\Framework\App\Request\CsrfValidator;

/**
 * Interceptor class for @see \Magento\Framework\App\Request\CsrfValidator
 */
class Interceptor extends \Magento\Framework\App\Request\CsrfValidator implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory, \Magento\Framework\App\State $appState)
    {
        $this->___init();
        parent::__construct($formKeyValidator, $redirectFactory, $appState);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(\Magento\Framework\App\RequestInterface $request, \Magento\Framework\App\ActionInterface $action) : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        $pluginInfo ? $this->___callPlugins('validate', func_get_args(), $pluginInfo) : parent::validate($request, $action);
    }
}
