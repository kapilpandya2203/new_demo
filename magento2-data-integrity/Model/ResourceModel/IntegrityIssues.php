<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class IntegrityIssues extends AbstractDb
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('data_integrity_issues', 'issue_id');
    }
}
