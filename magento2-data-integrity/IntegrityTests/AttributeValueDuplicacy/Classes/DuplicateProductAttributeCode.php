<?php

namespace Salecto\DataIntegrity\IntegrityTests\AttributeValueDuplicacy\Classes;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\Entity\Attribute; // Import the Attribute class
use Magento\Store\Model\StoreManagerInterface; // Import StoreManagerInterface
use Magento\Framework\Api\FilterBuilder;

class DuplicateProductAttributeCode
{
    protected ProductAttributeRepositoryInterface $productAttributeRepository;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected ProductRepository $productRepository;
    protected StoreManagerInterface $storeManager;
    protected FilterBuilder $filterBuilder;

    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        FilterBuilder $filterBuilder // Add StoreManagerInterface dependency
    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Get duplicate product attributes with options and associated product IDs.
     *
     * @return array
     */
    public function getDuplicateAttributesWithOptionsAndProductIds(): array
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("---------getDuplicateAttributesWithOptionsAndProductIds---------------");

        $duplicateAttributesData = [];
        $filterSourceModel = $this->filterBuilder
            ->setField(Attribute::SOURCE_MODEL)
            ->setValue(array(null, 'Magento\Eav\Model\Entity\Attribute\Source\Table'))
            ->setConditionType('in')
            ->create();

        $filterFrontendInput = $this->filterBuilder
            ->setField(Attribute::FRONTEND_INPUT)
            ->setValue(array('multiselect', 'select'))
            ->setConditionType('in')
            ->create();
        // Retrieve all product attributes
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$filterSourceModel, $filterFrontendInput])
            ->create();
        // Retrieve attribute list
        $attributeList = $this->productAttributeRepository->getList($searchCriteria)->getItems();
        foreach ($attributeList as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $options = $attribute->getSource()->getAllOptions(true, true);

            $optionDataByLabel = [];
            foreach ($options as $option) {
                $optionLabel = strtolower((string)$option['label']);
                $optionId = $option['value'];
                $optionDataByLabel[$optionLabel][$optionId] = $option['label'];
            }
            // Check for duplicate option values
            foreach ($optionDataByLabel as $optionData) {
                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(print_r($optionData, true));

                if (count($optionData) > 1) {
                    // If duplicates found, add the attribute code, option label,
                    // and option data (ID and label) to the list
                    $duplicateOptions = [];
                    foreach ($optionData as $optionId => $optionLabel) {
                        $productIds = [];
                        foreach ($this->storeManager->getStores() as $store) {
                            $storeId = $store->getId();
                            $productIds[$storeId] = $this->getProductIdsForOption($attributeCode, $optionId, $storeId);
                        }

                        $duplicateOptions[$optionId] = [
                            'product_ids' => $productIds
                        ];
                    }
                    $duplicateAttributesData[] = [
                        'attribute_code' => $attributeCode,
                        'option_label' => $optionLabel,
                        'option_id_with_product_ids' => $duplicateOptions
                    ];
                }
            }
        }
        return $duplicateAttributesData;
    }

    /**
     * Get product IDs for a given attribute code and option ID.
     *
     * @param string $attributeCode
     * @param int $optionId
     * @param int $storeId
     * @return array
     */
    protected function getProductIdsForOption(string $attributeCode, int $optionId, int $storeId): array
    {
        $productIds = [];

        // Set store context
        $this->storeManager->setCurrentStore($storeId);

        // Create search criteria to find products by attribute code and option ID
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($attributeCode, $optionId, 'eq')
            ->addFilter('store_id', $storeId, 'eq')
            ->create();

        // Retrieve products matching the search criteria
        $productCollection = $this->productRepository->getList($searchCriteria);

        // Iterate through each product to collect product IDs
        foreach ($productCollection->getItems() as $product) {
            $productIds[] = $product->getId();
        }

        // Reset store context to default
        $this->storeManager->setCurrentStore(null);

        return $productIds;
    }
}
