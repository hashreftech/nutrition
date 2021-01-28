<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Cron;

use Firebear\ImportExport\Model\ExportJob\Processor;

/**
 * Class RunExportJobs
 *
 * @package Firebear\ImportExport\Cron
 */
class RunExportJobs
{
    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var \Firebear\ImportExport\Helper\Data
     */
    protected $helper;

    /**
     * RunExportJobs constructor.
     *
     * @param Processor $exportProcessor
     */
    public function __construct(
        Processor $exportProcessor,
        \Firebear\ImportExport\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->processor = $exportProcessor;
    }

    /**
     * @param $schedule
     *
     * @return bool
     */
    public function execute($schedule)
    {
        $jobCode = $schedule->getJobCode();
        preg_match('/_id_([0-9]+)/', $jobCode, $matches);
        if (isset($matches[1]) && (int)$matches[1] > 0) {
            $jobId = (int)$matches[1];
            $file = $this->helper->beforeRun($jobId);
            $history = $this->helper->createExportHistory($jobId, $file, 'cron');
            $this->processor->debugMode = $this->helper->getDebugMode();
            $this->processor->setLogger($this->helper->getLogger());
            $this->processor->inConsole = 1;
            $this->processor->process($jobId);
            $this->helper->saveFinishExHistory($history);

            return true;
        }

        return false;
    }
}
