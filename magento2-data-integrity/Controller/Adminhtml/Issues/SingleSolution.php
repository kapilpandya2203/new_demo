<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Controller\Adminhtml\Issues;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;

class SingleSolution extends Action
{
    public const ADMIN_RESOURCE = 'Salecto_DataIntegrity::data_integrity';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
