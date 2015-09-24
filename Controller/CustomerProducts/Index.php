<?php

namespace TurgayOzgur\C2C\Controller\CustomerProducts;

class Index extends \TurgayOzgur\C2C\Controller\Index
{
    /**
     * Load the page defined in view/frontend/layout/c2c_customer_products_index.xml
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }

}