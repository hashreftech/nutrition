<?php
namespace Sparsh\ProductLabel\Model\Product\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class File
 * @package Sparsh\ProductLabel\Model\Product\Attribute\Backend
 */
class File extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_file;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Construct
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_file = $file;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\DataObject $object
     * @return $this|\Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function afterSave($object)
    {
        if($this->request->getModuleName() != 'sparsh_product_label'){
            $path = $this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                'sparsh/product_label_image/'
            );

            $delete = $object->getData($this->getAttribute()->getName() . '_delete');

            if ($delete) {
                $fileName = $object->getData($this->getAttribute()->getName());

                $object->setData($this->getAttribute()->getName(), '');
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
                if ($this->_file->isExists($path.$fileName)) {
                    $this->_file->deleteFile($path.$fileName);
                }
            }

            try {
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory
                    ->create(['fileId' => 'product['.$this->getAttribute()->getName().']']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);
                $result = $uploader->save($path);
                $object->setData($this->getAttribute()->getName(), $result['file']);
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            } catch (\Exception $e) {
                if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                    $this->messageManager
                        ->addError(
                            $e
                                ->getMessage()." Please upload product label image with extensions jpg , jpeg , gif , png only."
                        );
                    $this->_logger->critical($e);
                }
            }

            return $this;
        }
    }
}
