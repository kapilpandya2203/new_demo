<?php

namespace Salecto\DataIntegrity\IntegrityTests\AttributeValueDuplicacy\Classes;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\Entity\Attribute; // Import the Attribute class
use Magento\Store\Model\StoreManagerInterface; // Import StoreManagerInterface
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\State;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;



class DuplicateProductAttributeCode
{
    protected ProductAttributeRepositoryInterface $productAttributeRepository;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    protected ProductRepository $productRepository;
    protected StoreManagerInterface $storeManager;
    protected FilterBuilder $filterBuilder;
    protected State $appState;
    protected AttributeCollectionFactory $_attributeFactory;
    protected ProductCollectionFactory $productCollectionFactory;



    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository,
        StoreManagerInterface $storeManager,
        State $appState,
        FilterBuilder $filterBuilder, // Add StoreManagerInterface dependency
        AttributeCollectionFactory $_attributeFactory,
        ProductCollectionFactory $productCollectionFactory


    ) {
        $this->productAttributeRepository = $productAttributeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->filterBuilder = $filterBuilder;
        $this->appState = $appState;
        $this->_attributeFactory = $_attributeFactory;
        $this->productCollectionFactory = $productCollectionFactory;

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
        $attributeCollection = $this->_attributeFactory->create();
        $attributeCollection->addFieldToFilter(\Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID, 4);

        // $attributeInfo->addFieldToFilter(Attribute::SOURCE_MODEL, 'null', 'Magento\Eav\Model\Entity\Attribute\Source\Table', 'in');
        $attributeCollection->addFieldToFilter('entity_type_id', 4)
            ->addFieldToFilter('frontend_input', ['in' => ['select', 'multiselect']])
            ->addFieldToFilter(
                ['source_model', 'source_model'],
                [
                    ['null' => true],
                    ['like' => 'Magento\Eav\Model\Entity\Attribute\Source\Table']
                ]
            );
        $attributeList = $attributeCollection->getItems();
        foreach ($attributeList as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(print_r($attributeCode, true));

            $options = $attribute->getSource()->getAllOptions(true, true);

            $optionDataByLabel = [];
            foreach ($options as $option) {
                $optionLabel = strtolower((string)$option['label']);
                $optionId = $option['value'];
                $optionDataByLabel[$optionLabel][$optionId] = $option['label'];
            }

            // Check for duplicate option values
            foreach ($optionDataByLabel as $optionData) {
                if (count($optionData) > 1) {
                    $duplicateOptions = [];
                    foreach ($optionData as $optionId => $optionLabel) {
                        $productIds = [];
                        foreach ($this->storeManager->getStores() as $store) {
                            $storeId = $store->getId();
                            $productIds[$storeId] = $this->getProductsByAttributeAndOption($attributeCode, $optionId, $storeId);
                        }

                        $duplicateOptions[$optionId] = [
                            'product_ids' => $productIds
                        ];
                    }
                    $duplicateAttributesData[] = [
                        'attribute_code' => $attributeCode,
                        'option_label' => $optionLabel,
                        'option_id_with_product_ids' =>  $duplicateOptions
                    ];
                }
            }
        }
        return $duplicateAttributesData;
    }

    /**
     * Get products with a specific attribute and option ID.
     *
     * @param string $attributeCode
     * @param int $optionId
     * @return array
     */
    public function getProductsByAttributeAndOption(string $attributeCode, int $optionId, int $storeId): array
    {
        $productIds = [];

        try {
            // Create product collection
            $productCollection = $this->productCollectionFactory->create();
            
            // Add filters based on parameters
            $productCollection->addAttributeToSelect('entity_id') // Only retrieve product IDs
                ->addAttributeToFilter($attributeCode, ['eq' => $optionId])
                ->setStoreId($storeId);

            // Retrieve product IDs
            foreach ($productCollection as $product) {
                $productIds[] = $product->getId();
            }
        } catch (\Exception $e) {
            // Handle exception
            \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Psr\Log\LoggerInterface::class)
                ->error("Error fetching products by attribute and option: " . $e->getMessage());
        }

        return $productIds;
    }
}
