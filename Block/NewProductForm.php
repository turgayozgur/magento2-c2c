<?php

namespace TurgayOzgur\C2C\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class NewProductForm extends Template
{
    /**
     * @param Template\Context $context
     * @param array $data
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(Template\Context $context,
                                array $data = [],
                                \Magento\Framework\UrlInterface $urlBuilder)
    {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Return the desired URL of a new product post action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_urlBuilder->getUrl('C2C/index/post');
    }
}