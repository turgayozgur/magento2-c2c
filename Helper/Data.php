<?php

namespace TurgayOzgur\C2C\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Allowed image extensions.
     *
     * @var array
     */
    private $_allowed_image_extensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * Allowed 3d object extensions
     *
     * @var array
     */
    private $_allowed_object_extensions = ['obj'];

    /**
     * Maximum size for image in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_IMAGE_FILE_SIZE = 1048576;

    /**
     * Maximum size for file in bytes
     * Default value is 1M
     *
     * @var int
     */
    const MAX_FILE_SIZE = 1048576;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    protected $httpFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory
    )
    {
        parent::__construct($context);

        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->httpFactory = $httpFactory;
    }

    /**
     * Upload product image.
     *
     * @param $fileId
     * @return string
     */
    public function uploadImage($fileId)
    {
        // Validate all of them first.
        if (!$this->isFileValid($fileId, true)) return [];

        /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
        $uploader = $this->_fileUploaderFactory->create(['fileId' => $fileId]);

        $uploader->setAllowedExtensions($this->_allowed_image_extensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'catalog/product/'
        );
        $uploader->save($path);

        $fullPath = $path . $uploader->getUploadedFileName();

        return $fullPath;
    }

    /**
     * Validate file.
     *
     * @throws \Exception
     * @param string $fileId
     * @param bool $isImage
     * @return bool
     */
    public function isFileValid($fileId, $isImage)
    {
        $adapter = $this->httpFactory->create();
        $adapter->addValidator(
            new \Zend_Validate_File_FilesSize(['max' => $isImage ? self::MAX_IMAGE_FILE_SIZE : self::MAX_FILE_SIZE])
        );

        if ($adapter->isUploaded($fileId)) {
            // validate image
            if (!$adapter->isValid($fileId)) {
                throw new \Exception(__('Uploaded file is not valid.'));
            }

            return true;
        }

        return false;
    }
}