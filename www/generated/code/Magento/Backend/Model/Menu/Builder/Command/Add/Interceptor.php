<?php
namespace Magento\Backend\Model\Menu\Builder\Command\Add;

/**
 * Interceptor class for @see \Magento\Backend\Model\Menu\Builder\Command\Add
 */
class Interceptor extends \Magento\Backend\Model\Menu\Builder\Command\Add implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(array $data = [])
    {
        $this->___init();
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function chain(\Magento\Backend\Model\Menu\Builder\AbstractCommand $command)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'chain');
        return $pluginInfo ? $this->___callPlugins('chain', func_get_args(), $pluginInfo) : parent::chain($command);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getId');
        return $pluginInfo ? $this->___callPlugins('getId', func_get_args(), $pluginInfo) : parent::getId();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $itemParams = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute($itemParams);
    }
}
