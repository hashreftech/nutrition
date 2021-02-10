<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
namespace Firebear\ImportExport\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Firebear\ImportExport\Helper\Spout as Helper;

/**
 * Spout message observer
 */
class SpoutMessageObserver implements ObserverInterface
{
    /**
     * Message Manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * Spout helper
     *
     * @var \Firebear\ImportExport\Helper\Spout
     */
    protected $_helper;

    /**
     * Initialize observer
     *
     * @param ManagerInterface $messageManager
     * @param Helper $helper
     */
    public function __construct(
        ManagerInterface $messageManager,
        Helper $helper
    ) {
        $this->_messageManager = $messageManager;
        $this->_helper = $helper;
    }

    /**
     * Add order condition to the SalesRule management
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->isSpoutInstall()) {
            $this->_messageManager->addNoticeMessage(
                __(
                    'To use the ODS and XLSX file format, you need to install '.
                    'the library Spout (composer require box/spout:~2.7).'
                )
            );
        }
    }
}
