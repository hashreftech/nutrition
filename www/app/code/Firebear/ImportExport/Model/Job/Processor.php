<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Job;

use Firebear\ImportExport\Model\Import\Adapter;
use Firebear\ImportExport\Model\Source\Type\File\Config;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\DecoderInterface;
use Magento\ImportExport\Controller\Adminhtml\ImportResult;
use Magento\ImportExport\Model\History;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregator;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import Job Processor.
 * Validate & import jobs launched by cron or by cli command
 */
class Processor
{
    /**
     * @var
     */
    public $strategy;
    public $errorsCount;
    public $debugMode;
    public $inConsole;
    public $reindex;
    public $outputModel;
    /**
     * @var \Firebear\ImportExport\Model\JobFactory
     */
    protected $jobFactory;
    /**
     * @var \Magento\ImportExport\Model\HistoryFactory
     */
    protected $importHistoryFactory;
    /**
     * @var \Firebear\ImportExport\Model\ImportFactory
     */
    protected $importFactory;
    /**
     * @var ObjectManagerFactory
     */
    protected $objectManagerFactory;
    /**
     * @var \Magento\Framework\FilesystemFactory
     */
    protected $filesystemFactory;
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var DecoderInterface $jsonDecoder
     */
    protected $jsonDecoder;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;
    /**
     * @var \Firebear\ImportExport\Model\Import
     */
    protected $importModel;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var StoreManagerInterface
     */
    protected $storeResolver;
    /**
     * @var \Firebear\ImportExport\Model\Job
     */
    protected $job;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;
    /**
     * @var UrlInterface
     */
    protected $backendUrl;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var History
     */
    protected $importHistoryModel;
    /**
     * Application
     *
     * @var \Magento\Framework\App\AreaList
     */
    protected $areaList;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $locale;
    protected $source;
    protected $typeSource;
    /**
     * @var Config
     */
    protected $typeConfig;
    protected $localeLocal;
    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $backendConfig;
    /**
     * @var \Magento\Backend\Model\Locale\Manager
     */
    protected $manager;
    /**
     * @var \Firebear\ImportExport\Model\Import\ProcessingErrorAggregator
     */
    protected $errorAggregator;
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexFactory;
    protected $indCollFactory;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    protected $fileWrite;

    /**
     * @var \Firebear\ImportExport\Model\Source\Factory
     */
    protected $sourceFactory;

    /**
     * @var \Firebear\ImportExport\Model\Source\Platform\Config
     */
    protected $configPlatform;

    /**
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var \Magento\Framework\TranslateInterface
     */
    protected $translator;

    /**
     * Processor constructor.
     * @param \Firebear\ImportExport\Model\JobFactory $jobFactory
     * @param \Firebear\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\Framework\FilesystemFactory $filesystemFactory
     * @param \Magento\ImportExport\Model\HistoryFactory $historyFactory
     * @param DecoderInterface $jsonDecoder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param UrlInterface $backendUrl
     * @param ConsoleOutput $output
     * @param Config $typeConfig
     * @param \Magento\Framework\App\AreaList $areaList
     * @param \Magento\Framework\Locale\Resolver $locale
     * @param \Magento\Framework\App\State $state
     * @param \Firebear\ImportExport\Model\Import\ProcessingErrorAggregator
     * @param \Magento\Indexer\Model\IndexerFactory
     * @param \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    public function __construct(
        \Firebear\ImportExport\Model\JobFactory $jobFactory,
        \Firebear\ImportExport\Model\ImportFactory $importFactory,
        \Magento\Framework\FilesystemFactory $filesystemFactory,
        \Magento\ImportExport\Model\HistoryFactory $historyFactory,
        DecoderInterface $jsonDecoder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        RequestInterface $request,
        LoggerInterface $logger,
        UrlInterface $backendUrl,
        ConsoleOutput $output,
        Config $typeConfig,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\Locale\Resolver $locale,
        \Magento\Framework\App\State $state,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\Backend\Model\Locale\ManagerFactory $manager,
        \Firebear\ImportExport\Model\Import\ProcessingErrorAggregator $errorAggregator,
        \Magento\Indexer\Model\IndexerFactory $indexFactory,
        \Magento\Framework\TranslateInterface $translator,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indCollFactory,
        \Firebear\ImportExport\Model\Output\Xslt $modelOutput,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\File\WriteFactory $fileWrite,
        \Magento\Framework\Registry $registry,
        \Firebear\ImportExport\Model\Source\Factory $sourceFactory,
        \Firebear\ImportExport\Model\Source\Platform\Config $configPlatform
    ) {
        $this->jobFactory = $jobFactory;
        $this->importFactory = $importFactory;
        $this->importHistoryFactory = $historyFactory;
        $this->filesystemFactory = $filesystemFactory;
        $this->jsonDecoder = $jsonDecoder;
        $this->logger = $logger;
        $this->request = $request;
        $this->backendUrl = $backendUrl;
        $this->timezone = $timezone;
        $this->output = $output;
        $this->typeConfig = $typeConfig;
        $this->locale = $locale;
        $this->areaList = $areaList;
        $this->state = $state;
        $this->localeLocal = null;
        $this->backendConfig = $backendConfig;
        $this->manager = $manager;
        $this->inConsole = 0;
        $this->errorAggregator = $errorAggregator;
        $this->indexFactory = $indexFactory;
        $this->indCollFactory = $indCollFactory;
        $this->translator = $translator;
        $this->outputModel = $modelOutput;
        $this->file = $file;
        $this->fileWrite = $fileWrite;
        $this->sourceFactory = $sourceFactory;
        $this->configPlatform = $configPlatform;

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
    }

    /**
     * @param $jobId
     * @param $file
     * @param int $offset
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processScope($jobId, $file, $offset = 1)
    {
        $totalTime = 0;
        $result = false;
        if (!$this->inConsole) {
            $this->output = null;
        }
        try {
            $data = $this->prepareJob($jobId);
            $importModel = $this->getImportModel();

            if (!$this->inConsole) {
                $data['output'] = null;
            }

            $data['file'] = $file;
            $data['offset'] = $offset;
            $this->strategy = $data['validation_strategy'];
            $this->errorsCount = $data['allowed_error_count'];
            $this->reindex = $data['reindex'];
            if (isset($data['type_file'])) {
                $this->setTypeSource($data['type_file']);
            }

            $importModel->setLogger($this->logger)->setData($data)->setJobId($jobId);

            if (!$this->inConsole) {
                $importModel->setOutput(null);
            }

            $importModel->setErrorAggregator($this->errorAggregator);
            $importModel->getErrorAggregator()->initValidationStrategy($this->strategy, $this->errorsCount);

            $validationResult = $this->dataValidate($data, $jobId);
            $area = $this->areaList->getArea($this->state->getAreaCode());

            $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);

            if ($this->strategy == 'validation-skip-errors') {
                $result = true;
            } else {
                $result = !$importModel->getErrorAggregator()->hasToBeTerminated();
            }

        } catch (\Exception $e) {
            $this->addLogComment(
                'Job #' . $jobId . ' can\'t be imported. Check if job exist',
                $this->output,
                'error'
            );

            $this->addLogComment(
                $e->getMessage(),
                $this->output,
                'error'
            );
        }

        $this->revertLocale();

        return $result;
    }

    /**
     * Prepare import job object. Load behavior & source data.
     *
     * @param int $jobId
     *
     * @return array
     */
    public function prepareJob($jobId)
    {
        $this->getImportModel()->setLogger($this->logger);
        $this->job = $this->jobFactory->create();
        $this->job->load($jobId);
        $data = [];
        if ($this->job->getId()) {
            $behaviorData = $this->jsonDecoder->decode($this->job->getBehaviorData());
            $sourceData = $this->jsonDecoder->decode($this->job->getSourceData());
            if (isset($sourceData['language']) && $sourceData['language']) {
                $this->changeLocal($sourceData['language']);
            }
            $iter = [];
            $identicaly = [];
            $mapAttributesData = [];
            foreach ($this->job->getMap() as $map) {
                $mapAttributesData[$map->getId()] = [
                    'system' => $map->getAttributeId() ? $map->getAttributeId() : $map->getSpecialAttribute(),
                    'import' => $map->getImportCode(),
                    'default' => $map->getDefaultValue()
                ];
                if (!in_array($map->getImportCode(), $iter)) {
                    $iter[] = $map->getImportCode();
                } else {
                    if ($map->getImportCode() != '') {
                        $identicaly[] = [
                            'system' => $map->getAttributeId() ? $map->getAttributeId() : $map->getSpecialAttribute(),
                            'import' => $map->getImportCode()
                        ];
                    }
                }
            }
            $priceRules = [];

            if (!empty($this->job->getPriceRules())) {
                $priceRules = \Zend\Serializer\Serializer::unserialize($this->job->getPriceRules());
            }
            $this->addLogComment(__('Entity %1', $this->job->getEntity()), $this->output, 'info');

            $data = array_merge(
                ['entity' => $this->job->getEntity()],
                $behaviorData,
                ['import_source' => $this->job->getImportSource()],
                $sourceData,
                [$this->job->getImportSource() . '_file_path' => $sourceData['file_path']],
                ['map' => $mapAttributesData],
                ['price_rules' => $priceRules],
                ['identicaly' => $identicaly],
                ['xslt' => $this->job->getXslt()],
                ['translate_from' => $this->job->getTranslateFrom()],
                ['translate_to' => $this->job->getTranslateTo()]
            );
        }

        if (isset($data['import_images_file_dir']) && !($data['import_images_file_dir'])) {
            unset($data['import_images_file_dir']);
        }

        return $data;
    }

    /**
     * Get import model
     *
     * @return \Firebear\ImportExport\Model\Import
     */
    public function getImportModel()
    {
        if (!$this->importModel) {
            $this->importModel = $this->importFactory->create();
        }

        return $this->importModel;
    }

    /**
     * @param $local
     */
    public function changeLocal($local)
    {

        $this->setLocal($this->locale->getLocale());
        if (!$this->inConsole) {
            $this->backendConfig->setValue('general/locale/code', $local);
            $this->locale->setLocale($local);
            $this->manager->create()->switchBackendInterfaceLocale($this->locale->getLocale());
        } else {
            $this->locale->setLocale($local);
            $this->translator->setLocale($local)->loadData(null, true);
        }

        $area = $this->areaList->getArea($this->state->getAreaCode());
        $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);
    }

    /**
     * @param $local
     * @return $this
     */
    public function setLocal($local)
    {
        $this->localeLocal = $local;

        return $this;
    }

    /**
     * @param $debugData
     * @param OutputInterface|null $output
     * @param null $type
     * @return $this
     */
    public function addLogComment($debugData, OutputInterface $output = null, $type = null)
    {
        if (!empty($this->logger->getFilename())) {
            $this->logger->info($debugData);
        }
        if ($output) {
            switch ($type) {
                case 'error':
                    $debugData = '<error>' . $debugData . '</error>';
                    break;
                case 'info':
                    $debugData = '<info>' . $debugData . '</info>';
                    break;
                default:
                    $debugData = '<comment>' . $debugData . '</comment>';
                    break;
            }
            $output->writeln($debugData);
        }

        return $this;
    }

    /**
     * @param $data
     * @param $jobId
     *
     * @return bool|int
     */
    public function dataValidate($data, $jobId)
    {
        $validationResult = 0;
        try {
            $validationResult = $this->validate($data);
        } catch (\Exception $e) {
            $this->getImportModel()->addLogComment($e->getMessage());
            $summary = '<b>' . $e->getMessage() . '</b><br />';
            $importHistoryModel = $this->getImportHistoryModel();
            $importHistoryModel->load($importHistoryModel->getLastItemId());
            $date = $this->getTimezone()->formatDateTime(
                $this->getTimezone()->date(),
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::MEDIUM,
                null,
                null
            );
            $summary .= '<i>' . $date . '</i><br />';
            $summary .= 'Job: #' . $jobId;
            $importHistoryModel->setSummary($summary);
            $importHistoryModel->setExecutionTime(History::IMPORT_FAILED);
            $importHistoryModel->save();
            $validationResult = false;
        }

        return $validationResult;
    }

    /**
     * @param array $data
     * @return bool
     * @throws LocalizedException
     */
    protected function validate(array $data)
    {
        $importModel = $this->getImportModel();

        $importModel->setData($data);
        $importModel->setJobId($this->job->getId());
        if (!$this->inConsole) {
            $importModel->setOutput(null);
        }

        $platform = $importModel->getPlatform(
            $data['platforms'] ?? null,
            $data['entity'] ?? null
        );

        $isGateway = $platform && $platform->isGateway();
        if ($isGateway) {
            $source = $platform->getSource($data);
        } else {
            if ($data['import_source'] != 'file') {
                $destFile = $importModel->uploadSource();
                //   $another = 1;
            } else {
                $destFile = $data['file_path'];
            }

            if (isset($data['type_file']) && $data['type_file'] == 'xml' && $data['xml_switch']) {
                $destFile = $this->applyXsltTemplate($destFile, $data);
            }

            $source = Adapter::findAdapterFor(
                $this->getTypeClass(),
                $destFile,
                $this->filesystemFactory->create()->getDirectoryWrite(DirectoryList::ROOT),
                $platform,
                $data
            );
        }

        $validationResult = $importModel->validateSource($source);

        if (!$importModel->getProcessedRowsCount()) {
            if (!$importModel->getErrorAggregator()->getErrorsCount()) {
                throw new LocalizedException(
                    __('This file is empty. Please try another one.')
                );
            } else {
                $errors = '';
                foreach ($importModel->getErrorAggregator()->getAllErrors() as $error) {
                    $errors .= $error->getErrorMessage() . ' ';
                }
                throw new LocalizedException(
                    __($errors)
                );
            }
        } else {
            if (!$validationResult) {
                throw new LocalizedException(
                    __('Data validation is failed. Please fix errors and re-upload the file..')
                );
            } else {
                if ($importModel->isImportAllowed()) {
                    return true;
                } else {
                    throw new LocalizedException(
                        __('The file is valid, but we can\'t import it for some reason.')
                    );
                }
            }
        }
    }

    /**
     * Apply XSLT template and save result to new xml file.
     *
     * @param $destFile
     * @param $data
     *
     * @return string
     * @throws LocalizedException
     */
    public function applyXsltTemplate($destFile, $data)
    {
        $directory = $this->filesystemFactory->create()->getDirectoryWrite(DirectoryList::ROOT);
        $file = $destFile;
        if (strpos($destFile, $directory->getAbsolutePath()) === false) {
            $file = $directory->getAbsolutePath() . "/" . $destFile;
        }
        $dest = $this->file->read($file);
        try {
            $result = $this->outputModel->convert($dest, $data['xslt']);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        $pathInfo = pathinfo($destFile);

        if (strpos($destFile, $directory->getAbsolutePath()) === false) {
            $destFile = $directory->getAbsolutePath() . '/' . $pathInfo['dirname'] . '/' .
                $pathInfo['filename'] . '_xslt.' . $pathInfo['extension'];
        } else {
            $destFile = $pathInfo['dirname'] . "/" . $pathInfo['filename'] . "_xslt." . $pathInfo['extension'];
        }

        $file = $this->fileWrite->create(
            $destFile,
            \Magento\Framework\Filesystem\DriverPool::FILE,
            "w+"
        );
        $file->write($result);
        $file->close();

        return $destFile;
    }

    /**
     * Check file modified date.
     *
     * @param Import $importModel
     * @param        $modifiedAt
     *
     * @return bool
     */
    public function checkModified(Import $importModel, $modifiedAt)
    {
        if ($importModel->getSource()) {
            return $importModel->getSource()->checkModified($modifiedAt);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getTypeClass()
    {
        $data = $this->typeConfig->get();
        $types = $data['import'];
        $value = current($types);
        $model = $value['model'];
        if (isset($types[$this->getTypeSource()])) {
            $model = $types[$this->getTypeSource()]['model'];
        }

        return $model;
    }

    /**
     * @return mixed
     */
    public function getTypeSource()
    {
        return $this->typeSource;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setTypeSource($type)
    {
        $this->typeSource = $type;

        return $this;
    }

    /**
     * Get import history model
     *
     * @return History|mixed
     */
    public function getImportHistoryModel()
    {
        if (!$this->importHistoryModel) {
            $this->importHistoryModel = $this->importHistoryFactory->create();
        }

        return $this->importHistoryModel;
    }

    /**
     * Get current timezone object.
     * We can't define timezone in constructor according to db lock timeout
     * when run job from console.
     *
     * @return \Magento\Framework\Stdlib\DateTime\Timezone|mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    public function revertLocale()
    {
        $this->locale->setLocale($this->getLocal());
        if (!$this->inConsole) {
            $this->backendConfig->setValue('general/locale/code', $this->getLocal());
            $this->locale->setLocale($this->getLocal());
            $this->manager->create()->switchBackendInterfaceLocale($this->getLocal());
        } else {
            $this->locale->setLocale($this->getLocal());
            $this->translator->setLocale($this->getLocal())->loadData(null, true);
        }
        $area = $this->areaList->getArea($this->state->getAreaCode());
        $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);
    }

    /**
     * @return null
     */
    public function getLocal()
    {
        return $this->localeLocal;
    }

    /**
     * @param $file
     * @param $job
     * @param $offset
     * @param $error
     * @param int $show
     * @return array
     * @throws LocalizedException
     */
    public function processImport($file, $job, $offset, $error, $show = 1)
    {
        if (!$this->inConsole) {
            $this->output = null;
        }
        $data = $this->prepareJob($job);
        if (!$this->inConsole) {
            $data['output'] = null;
        }
        $this->logger->setFileName($file);
        $this->strategy = $data['validation_strategy'];
        $this->errorsCount = $data['allowed_error_count'];
        if (isset($data['type_file'])) {
            $this->setTypeSource($data['type_file']);
        }

        /**
         * @todo Check another entities for fetching data from db in constructor.
         * Avoid new entity object initialization if possible.
         */
        if ($data['entity'] != 'catalog_product') {
            $this->importModel = $this->importFactory->create();
        }
        $this->importModel->setLogger($this->logger);
        $this->importModel->setData($data);
        $this->importModel->setJobId($job);
        if (!$this->inConsole) {
            $this->importModel->setOutput(null);
        }
        $this->importModel->setErrorAggregator($this->errorAggregator);
        $this->importModel->getErrorAggregator()->initValidationStrategy($this->strategy, $this->errorsCount);
        $this->importModel->setProcessErrorStatistics(ProcessingError::ERROR_LEVEL_CRITICAL, $error);
        $this->importModel->importSourcePart($file, $offset, $job, $show);
        $this->scopeMessages(1);
        $this->revertLocale();

        return [
            (int) $this->importModel->getErrorsCount([ProcessingError::ERROR_LEVEL_CRITICAL]),
            !$this->importModel->getErrorAggregator()->hasToBeTerminated()
        ];
    }

    /**
     * @param int $skip
     *
     * @throws LocalizedException
     */
    protected function scopeMessages($skip = 0)
    {
        if ($this->getImportModel()->getErrorAggregator()->hasToBeTerminated()) {
            $messages = [
                __('Maximum error count has been reached or system error is occurred!')
            ];

            foreach ($this->getImportModel()->getErrorAggregator()->getAllErrors() as $error) {
                $messages[] = $error->getErrorMessage();
                if ($skip) {
                    $this->addLogComment($error->getErrorMessage(), $this->output, 'error');
                }
            }
            if (!$skip) {
                throw new LocalizedException(
                    __(implode(PHP_EOL, $messages))
                );
            }
        }
    }

    /**
     * @param $file
     * @param $job
     * @return bool
     */
    public function processReindex($file, $job)
    {
        try {
            $this->logger->setFileName($file);
            $this->reindex();
        } catch (\Exception $e) {
            $this->addLogComment($e->getMessage(), $this->output, 'error');

            return false;
        }

        return true;
    }

    public function reindex()
    {
        $this->addLogComment(__('Running REINDEX'), $this->output, 'info');
        $indexerCollection = $this->indCollFactory->create();
        $indexers = $indexerCollection->getItems();
        foreach ($indexers as $item) {
            $this->addLogComment(__('REINDEX %1', $item->getTitle()), $this->output, 'info');
            $item->reindexAll();
        }
        $this->addLogComment(__('REINDEX completed'), $this->output, 'info');
    }

    /**
     * @param $jobId
     * @return bool
     */
    public function process($jobId)
    {
        $totalTime = 0;
        $result = false;
        try {
            $timeStart = time();
            $data = $this->prepareJob($jobId);
            $this->strategy = $data['validation_strategy'];
            if (isset($data['type_file'])) {
                $this->setTypeSource($data['type_file']);
            }
            $validationResult = $this->dataValidate($data, $jobId);
            if ($validationResult || $this->strategy != ProcessingErrorAggregator::VALIDATION_STRATEGY_STOP_ON_ERROR) {
                if ($this->strategy != ProcessingErrorAggregator::VALIDATION_STRATEGY_STOP_ON_ERROR) {
                    $this->scopeMessages(1);
                    $this->getImportModel()->getErrorAggregator()->clear();
                }
                $this->importModel = $this->importFactory->create();
                $this->getImportModel()->setLogger($this->logger);
                $this->getImportModel()->setData($data);
                $this->getImportModel()->setJobId($jobId);

                $this->getImportModel()->importSource();
                $modified = $this->checkModified($this->getImportModel(), $this->job->getFileUpdatedAt());
                if (is_int($modified)) {
                    $this->job->setFileUpdatedAt($modified)->save();
                }

                $this->getImportModel()->invalidateIndex();
            }

            $timeFinish = time();
            $totalTime = $timeFinish - $timeStart;
            $counter = 0;
            if ($this->getImportModel()) {
                $errorAggregator = $this->getImportModel()->getErrorAggregator();
                $messages = [];
                $rowMessages = $errorAggregator->getRowsGroupedByErrorCode(
                    [],
                    [AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION]
                );
                foreach ($rowMessages as $errorCode => $rows) {
                    $messages[] = $errorCode . ' ' . __('in rows:') . ' ' . implode(', ', $rows);
                }

                foreach ($messages as $error) {
                    ++$counter;
                    $this->addLogComment($counter . '. ' . $error, $this->output, 'error');

                    if ($counter >= ImportResult::LIMIT_ERRORS_MESSAGE) {
                        break;
                    }
                }
                if ($errorAggregator->hasFatalExceptions()) {
                    $errorsByCode = $errorAggregator->getErrorsByCode(
                        [AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION]
                    );
                    foreach ($errorsByCode as $error) {
                        $this->addLogComment(
                            $error->getErrorMessage(),
                            $this->output,
                            'error'
                        );
                        $this->addLogComment(
                            $error->getErrorDescription(),
                            $this->output,
                            'error'
                        );
                    }
                } else {
                    $result = true;
                }
            }
        } catch (\Exception $e) {
            $this->addLogComment(
                'Job #' . $jobId . ' can\'t be imported. Check if job exist',
                $this->output,
                'error'
            );
            $this->addLogComment(
                $e->getMessage(),
                $this->output,
                'error'
            );
        }
        if ($totalTime) {
            $this->addLogComment(
                'Job #' . $jobId . ' was generated successfully in ' . $totalTime . ' seconds',
                $this->output,
                'info'
            );
        }
        $area = $this->areaList->getArea($this->state->getAreaCode());
        $area->load(\Magento\Framework\App\Area::PART_TRANSLATE);

        $this->revertLocale();

        return $result;
    }

    /**
     * Get columns names from first row
     *
     * @param \Firebear\ImportExport\Model\Job $job
     *
     * @return array
     */
    public function getColumns($job)
    {
        $errorMessage = [];
        if (is_object($job) && (!$job->getId() || $job->getEntity() != 'catalog_product')) {
            return [];
        }

        $data = is_object($job) ? $this->prepareJob($job->getId()) : $job;
        if (isset($data['job_id'])) {
            $jobId = (int)$data['job_id'];
            $jobModel = $this->jobFactory->create();
            $jobModel->load($jobId);
            $sourceData = $this->jsonDecoder->decode($jobModel->getSourceData());
            if (isset($sourceData['xml_switch']) && $sourceData['xml_switch'] && $jobModel->getXslt()) {
                $data['xml_switch'] = 1;
                $data['xslt'] = $jobModel->getXslt();
            }
        }

        $directory = $this->filesystemFactory->create()->getDirectoryWrite(DirectoryList::ROOT);
        if (!$this->inConsole) {
            $this->getImportModel()->setOutput(null);
        }

        $platform = $this->getImportModel()->getPlatform(
            $data['platforms'] ?? null,
            $data['entity'] ?? null
        );

        if ($data['import_source'] == 'file') {
            $destFile = $data['file_path'];

            if (isset($data['type_file']) && $data['type_file'] == 'xml'
                && isset($data['xml_switch']) && $data['xml_switch']
            ) {
                $destFile = $this->applyXsltTemplate($destFile, $data);
            }

            $source = Adapter::findAdapterFor(
                $this->getTypeClass(),
                $destFile,
                $this->filesystemFactory->create()->getDirectoryWrite(DirectoryList::ROOT),
                $platform,
                $data
            );
        } else {
            $isGateway = $platform && $platform->isGateway();
            if ($isGateway) {
                $data['get_only_first_page'] = true;
                $this->source = $platform->getSource($data);
                $this->getImportModel()->setSource($this->source);
            } else {
                $this->getImportModel()->setImportSource($data['import_source']);
            }

            $this->getImportModel()->setData($data);
            $this->getImportModel()->getSource()->setData($data);
            $this->getImportModel()->setLogger($this->logger);

            if ($isGateway) {
                return $this->source->getColNames();
            }

            $result = null;
            $source = $this->getImportModel()->getSource();
            $source->setFormatFile($data['type_file']);
            try {
                $result = $source->uploadSource();
            } catch (\Exception $e) {
                $errorMessage = __($e->getMessage());
                if (strpos($errorMessage, 'ftp_get()') !== false) {
                    $errorMessage = __('Unable to open your file. Please make sure File Path is correct.');
                }
            }
            $destFile = $this->getImportModel()->uploadSource();
            if (isset($data['type_file']) && $data['type_file'] == 'xml'
                && isset($data['xml_switch']) && $data['xml_switch']
            ) {
                $destFile = $this->applyXsltTemplate($destFile, $data);
            }

            if ($result) {
                $source = Adapter::findAdapterFor(
                    $this->getTypeClass(),
                    $destFile,
                    $directory,
                    $platform,
                    $data
                );
            } else {
                $this->source = $source;

                return is_array($job) ? $errorMessage : [];
            }
        }
        $this->source = $source;

        return $source->getColNames();
    }

    public function correctData($data)
    {
        $errorMessage = [];

        $data = $this->prepareDataFromAjax($data);
        if (isset($data['job_id'])) {
            $jobId = (int)$data['job_id'];
            $jobModel = $this->jobFactory->create();
            $jobModel->load($jobId);
            $sourceData = $this->jsonDecoder->decode($jobModel->getSourceData());
            if (isset($sourceData['xml_switch']) && $sourceData['xml_switch'] && $jobModel->getXslt()) {
                $data['xml_switch'] = 1;
                $data['xslt'] = $jobModel->getXslt();
            } else {
                $data['xml_switch'] = 0;
            }
        }
        if (!$this->inConsole) {
            $this->getImportModel()->setOutput(null);
        }

        $platform = $this->getImportModel()->getPlatform(
            $data['platforms'] ?? null,
            $data['entity'] ?? null
        );

        if ($data['import_source'] == 'file') {
            $destFile = $data['file_path'];
            if (isset($data['type_file']) && $data['type_file'] == 'xml' && $data['xml_switch']) {
                $destFile = $this->applyXsltTemplate($destFile, $data);
            }
            $source = Adapter::findAdapterFor(
                $this->getTypeClass(),
                $destFile,
                $this->filesystemFactory->create()
                    ->getDirectoryWrite(DirectoryList::ROOT),
                $platform,
                $data
            );
        } else {
            $isGateway = $platform && $platform->isGateway();
            if ($isGateway) {
                $data['get_only_first_page'] = true;
                $this->source = $platform->getSource($data);
                $this->getImportModel()->setSource($this->source);
            } else {
                $this->getImportModel()->setImportSource($data['import_source']);
            }
            $this->getImportModel()->setData($data);
            $this->getImportModel()->getSource()->setData($data);

            if ($isGateway) {
                $this->source->setMap($data['records']);
                return true;
            }

            $result = null;
            $source = $this->getImportModel()->getSource();
            try {
                $result = $source->uploadSource();
            } catch (\Exception $e) {
                $errorMessage = __($e->getMessage());
                if (strpos($errorMessage, 'ftp_get()') !== false) {
                    $errorMessage = __('Unable to open your CSV file. Please make sure File Path is correct.');
                }
            }

            if ($result) {
                $destFile = $this->getImportModel()->uploadSource();
                if (isset($data['type_file']) && $data['type_file'] == 'xml' && $data['xml_switch']) {
                    $destFile = $this->applyXsltTemplate($destFile, $data);
                }
                $source = Adapter::findAdapterFor(
                    $this->getTypeClass(),
                    $destFile,
                    $this->filesystemFactory->create()->getDirectoryWrite(DirectoryList::ROOT),
                    $platform,
                    $data
                );
            } else {
                $this->source = $source;
            }
        }
        $this->source = $source;
        $this->source->setMap($data['records']);
        return true;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function prepareDataFromAjax($data)
    {
        $mapAttributesData = [];
        foreach ($data['records'] as $map) {
            $mapAttributesData[] = [
                'system' => $map['source_data_system'],
                'import' => $map['source_data_import'],
                'default' => $map['source_data_replace']
            ];
        }
        $data['records'] = $mapAttributesData;
        return $data;
    }

    public function validateFile()
    {
        $source = $this->getTypeSource();
    }

    /**
     * @param $data
     *
     * @return array
     * @throws LocalizedException
     */
    public function processValidate($data)
    {
        if (isset($data['locale']) && $data['locale']) {
            $this->changeLocal($data['locale']);
        }
        $data['output'] = null;
        $messages = [];
        if ($this->source !== null) {
            $this->getImportModel()->setData($data);
            if (!$this->inConsole) {
                $this->getImportModel()->setOutput(null);
            }
            $validationResult = $this->getImportModel()->validateCheck($this->source);
            $errorAggregator = $this->getImportModel()->getErrorAggregator();
            foreach ($errorAggregator->getAllErrors() as $error) {
                $messages[] = $error->getErrorMessage();
            }
        }

        return empty($messages) ? [] : $messages;
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function showErrors()
    {
        $this->getImportModel()->showErrors();
    }
}
