<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Controller\Adminhtml\Issues;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Salecto_DataIntegrity::issue_listing';

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create($this->resultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Salecto_DataIntegrity::data_integrity_listing');
        $resultPage->getConfig()->getTitle()->prepend(__('Data Integrity'));
        return $resultPage;
    }
}
