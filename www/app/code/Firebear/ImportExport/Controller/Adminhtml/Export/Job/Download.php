<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Controller\Adminhtml\Export\Job;

use Firebear\ImportExport\Controller\Adminhtml\Export\Context;
use Firebear\ImportExport\Controller\Adminhtml\Export\Job as JobController;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Filesystem;

/**
 * Class Download
 * @package Firebear\ImportExport\Controller\Adminhtml\Export\Job
 */
class Download extends JobController
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);

        $this->fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
    }

    /**
     * Execute action
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $file = $this->getRequest()->getParam('file');
        $file = str_replace("|", "/", $file);

        return $this->downloadFile($file);
    }

    /**
     * @param $file
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function downloadFile($file)
    {
        $file = $this->directory->getAbsolutePath() . $file;

        return $this->fileFactory->create(basename($file), file_get_contents($file), DirectoryList::LOG);
    }
}
