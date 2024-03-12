<?php

declare(strict_types=1);

namespace Salecto\DataIntegrity\IntegrityTests\Classes;

use Magento\Store\Api\StoreRepositoryInterface;

class StoresUtil
{
    protected StoreRepositoryInterface $storeRepository;

    public function __construct(
        StoreRepositoryInterface $storeRepository
    )
    {
        $this->storeRepository = $storeRepository;
    }

    /**
     * @return array<int>
     */
    public function getAllStoreIds(): array
    {
        $storeIds = [];

        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            $storeIds[] = $store->getId();
        }

        return $storeIds;
    }
}
