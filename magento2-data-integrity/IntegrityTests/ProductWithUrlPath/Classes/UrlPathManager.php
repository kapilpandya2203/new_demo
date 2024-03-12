<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\ProductWithUrlPath\Classes;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValueFactory as AttributeScopeOverriddenValueFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\Exception\LocalizedException;

class UrlPathManager
{
    public const ATTRIBUTE_CODE = 'url_path';
    private const CATALOG_PRODUCT_ENTITY_VARCHAR_TABLE = 'catalog_product_entity_varchar';
    protected AttributeScopeOverriddenValueFactory $attributeScopeOverriddenValueFactory;
    protected AttributeResource $attributeResource;
    protected AttributeFactory $attributeFactory;

    public function __construct(
        AttributeScopeOverriddenValueFactory $attributeScopeOverriddenValueFactory,
        AttributeResource                    $attributeResource,
        AttributeFactory                     $attributeFactory
    )
    {
        $this->attributeScopeOverriddenValueFactory = $attributeScopeOverriddenValueFactory;
        $this->attributeResource = $attributeResource;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * returns whether a url_path attribute is overriden for the product on the store
     *
     * @param ProductInterface $product
     * @param int $storeId
     * @return mixed
     */
    public function isOverridenInStore(ProductInterface $product, int $storeId)
    {
        return $this
            ->attributeScopeOverriddenValueFactory
            ->create()
            ->containsValue(ProductInterface::class, $product, self::ATTRIBUTE_CODE, $storeId);
    }

    /**
     * Returns an array for the url path attribute with value & value id
     * for a product & store. Returns an empty array if the url path attribute
     * doesn't exist
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    public function getAttributeValue(int $productId, int $storeId)
    {
        $attribute = [];

        $urlPathAttribute = $this->attributeFactory->create()
            ->loadByCode(Product::ENTITY, self::ATTRIBUTE_CODE);
        $urlPathAttributeId = $urlPathAttribute->getId();

        $connection = $this->attributeResource->getConnection();

        $select = $connection->select();
        $select->from($this->attributeResource->getTable(self::CATALOG_PRODUCT_ENTITY_VARCHAR_TABLE));
        $select->where('entity_id = :product_id');
        $select->where('store_id = :store_id');
        $select->where('attribute_id = :url_path_attribute_id');

        $bind = [
            'product_id' => $productId,
            'store_id' => $storeId,
            'url_path_attribute_id' => $urlPathAttributeId
        ];

        $row = $connection->fetchRow($select, $bind);

        if (!empty($row)) {
            $attribute['url_path_value'] = $row['value'];
            $attribute['url_path_value_id'] = $row['value_id'];
        }

        return $attribute;
    }

    /**
     * Deletes the url path value having id $urlPathValueId
     *
     * @param $urlPathValueId
     * @return void
     */
    public function deleteById($urlPathValueId)
    {
        $connection = $this->attributeResource->getConnection();
        $connection->delete($this->attributeResource->getTable(self::CATALOG_PRODUCT_ENTITY_VARCHAR_TABLE), ['value_id = ?' => $urlPathValueId]);
    }
}
