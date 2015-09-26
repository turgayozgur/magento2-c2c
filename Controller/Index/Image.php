<?php

namespace TurgayOzgur\C2C\Controller\Index;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\View\Result\PageFactory;

class Image extends \TurgayOzgur\C2C\Controller\Index
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        ActionContext $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context, $resultPageFactory);

        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Post new product
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $request = $this->getRequest();

        $post = $request->getPostValue();
        if (!$post) {
            return $resultJson->setData(
                [
                    'error' => 400
                ]
            );
        }

        /** @var \TurgayOzgur\C2C\Helper\Data $dataHelper */
        $dataHelper = $this->_objectManager->get('TurgayOzgur\C2C\Helper\Data');

        $path = $dataHelper->uploadImage('file');

        return $resultJson->setData(
            [
                'success' => 200,
                'path' => $path,
            ]
        );
    }

}