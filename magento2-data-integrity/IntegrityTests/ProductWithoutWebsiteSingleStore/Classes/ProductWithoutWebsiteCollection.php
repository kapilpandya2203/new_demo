<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithoutWebsiteSingleStore\Classes;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductWithoutWebsiteCollection
{
    protected CollectionFactory $productCollectionFactory;

    public function __construct(CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Returns the collection of products without websites
     * @return Collection
     */
    protected function getProductCollection(): Collection
    {
        $collection = $this->productCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['website' => $collection->getTable('catalog_product_website')],
            'e.entity_id = website.product_id',
            []
        );
        $collection->getSelect()->where('website.product_id IS NULL');

        return $collection;
    }

    /**
     * Returns product ids that don't have an associated website
     * @return array
     */
    public function getProductIds(): array
    {
        $collection = $this->getProductCollection();
        $collection->addAttributeToSelect('entity_id');

        return $collection->getColumnValues('entity_id');
    }
}
