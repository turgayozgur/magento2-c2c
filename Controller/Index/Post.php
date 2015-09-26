<?php

namespace TurgayOzgur\C2C\Controller\Index;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Catalog\Controller\Adminhtml\Product\Builder as ProductBuilder;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as ProductInitializationHelper;
use Magento\Framework\View\Result\PageFactory;

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
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
    )
    {
        $this->initializationHelper = $initializationHelper;
        $this->productBuilder = $productBuilder;
        $this->productTypeManager = $productTypeManager;
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

        /** @var \TurgayOzgur\C2C\Model\ProductFromCustomer $product */
        $product = $this->initializationHelper->initialize($this->productBuilder->build($request));
        $this->productTypeManager->processProduct($product);

        $imagePaths = $request->getParam('image');

        if ($imagePaths)
        {
            $isFirst = true;
            foreach($imagePaths as $path)
            {
                $product->addImageToMediaGallery($path, $isFirst ? ['image', 'small_image', 'thumbnail'] : null, false, false);
                $isFirst = false;
            }
        }

        $product->setTypeId('simple'); // Simple.
        $product->setAttributeSetId(4); // Default.
        $product->setStatus(1); // 1 - Enable, 2 - Disable.
        $product->setVisibility(4);
        $product->setSku($product->getName());
        $product->setCustomerId($this->_getSession()->getCustomerId());

        $product->save();

        $resultRedirect->setPath('C2C/customerproducts');
        return $resultRedirect;
    }

}