<?php

namespace TurgayOzgur\C2C\Controller\Index;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as ProductInitializationHelper;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Post extends \TurgayOzgur\C2C\Controller\Index
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $productTypeManager;

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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper
     * @param PageFactory $resultPageFactory
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     */
    public function __construct(
        ActionContext $context,
        ProductBuilder $productBuilder,
        ProductInitializationHelper $initializationHelper,
        PageFactory $resultPageFactory,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->initializationHelper = $initializationHelper;
        $this->productBuilder = $productBuilder;
        $this->productTypeManager = $productTypeManager;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($context, $resultPageFactory);
    }

    /**
     * Post new product
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();

        $post = $request->getPostValue();
        if (!$post) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
        $uploader = $this->_fileUploaderFactory->create(['fileId' => 'image']);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'catalog/product/'
        );
        $uploader->save($path);

        /** @var \TurgayOzgur\C2C\Model\ProductFromCustomer $product */
        $product = $this->initializationHelper->initialize($this->productBuilder->build($request));
        $this->productTypeManager->processProduct($product);

        $product->setTypeId('simple');
        $product->setAttributeSetId(4); // Default.
        $product->setSku($product->getName());
        $product->setCustomerId($this->_getSession()->getCustomerId());
        $product->setVisibility(4);

        $product->addImageToMediaGallery($path . $uploader->getUploadedFileName(), ['image', 'small_image', 'thumbnail'], false, false);

        $product->save();

        $resultRedirect->setPath('C2C');
        return $resultRedirect;
    }

}