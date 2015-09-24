<?php

namespace TurgayOzgur\C2C\Api\Data;

/**
 * @api
 */
interface ProductFromCustomerInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const CUSTOMER_ID = 'customer_id';
    /**#@-*/

    /**
     * Product customer_id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set product customer_id
     *
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
}