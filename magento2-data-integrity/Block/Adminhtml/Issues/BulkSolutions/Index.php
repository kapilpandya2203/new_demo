<?php

declare(strict_types=1);

namespace Salecto\DataIntegrity\Block\Adminhtml\Issues\BulkSolutions;

use Magento\Backend\Block\Template;

class Index extends Template
{
    public function getFormAction()
    {
        return $this->getUrl('data_integrity/issues/bulksolutionsapply', ['_secure' => true]);
    }
}
