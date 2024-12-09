<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Salecto\DataIntegrity\Model\IntegrityIssues as Model;
use Salecto\DataIntegrity\Model\ResourceModel\IntegrityIssues as ResourceModel;

/**
 * Integrity Issues collection
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct(): void
    {
        $this->_init(
            Model::class,
            ResourceModel::class
        );
    }
}
