<?php

namespace TurgayOzgur\C2C\Block;

use TurgayOzgur\C2C\Api\Data\ProductFromCustomerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Resource\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\CatalogWidget\Model\Rule;
use Magento\Widget\Helper\Conditions;

/**
 * Class ProductList
 */
class ProductList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Catalog\Block\Product\Context|Context $context
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Context $context,
        ProductCollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        HttpContext $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        array $data = []
    ) {
        parent::__construct($context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data);
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getProducts()
    {
        $filters = $this->buildFilters();
        $searchCriteria = $this->buildSearchCriteria($filters);
        return $this->productRepository->getList($searchCriteria);
    }

    /**
     * @return \Magento\Framework\Api\Filter[]
     */
    private function buildFilters()
    {
        $typeFilter = $this->filterBuilder
            ->setField(ProductFromCustomerInterface::CUSTOMER_ID)
            ->setValue(2)
            ->create();
        $filters[] = $typeFilter;

        return $filters;
    }

    /**
     * @param \Magento\Framework\Api\Filter[] $filters
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    private function buildSearchCriteria(array $filters)
    {
        return $this->searchCriteriaBuilder->addFilters($filters)->create();
    }
}