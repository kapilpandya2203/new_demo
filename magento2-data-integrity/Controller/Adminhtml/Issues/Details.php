<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Controller\Adminhtml\Issues;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesRepositoryInterface;
use Salecto\DataIntegrity\Model\IntegrityIssuesFactory;
use Salecto\DataIntegrity\Model\IntegrityTests;

class Details extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Salecto_DataIntegrity::data_integrity';

    protected PageFactory $resultPageFactory;
    protected JsonFactory $resultJsonFactory;
    protected Http $request;
    protected IntegrityIssuesFactory $issuesFactory;
    protected IntegrityTests $integrityTests;
    protected LayoutFactory $layoutFactory;
    protected IntegrityIssuesRepositoryInterface $issuesRepository;

    public function __construct(
        Context                            $context,
        PageFactory                        $resultPageFactory,
        JsonFactory                        $resultJsonFactory,
        Http                               $request,
        IntegrityIssuesFactory             $issuesFactory,
        IntegrityTests                     $integrityTests,
        IntegrityIssuesRepositoryInterface $issuesRepository,
        LayoutFactory                      $layoutFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->issuesFactory = $issuesFactory;
        $this->integrityTests = $integrityTests;
        $this->layoutFactory = $layoutFactory;
        $this->issuesRepository = $issuesRepository;
    }


    public function execute()
    {
        $issueId = $this->request->getParam('issue');
        $issue = $this->issuesRepository->getById($issueId);
        $test = $this->integrityTests->getTestByCode($issue->getTestCode());

        $layout = $this->layoutFactory->create();
        $readmeHtml = $layout->createBlock(Template::class)
            ->setTemplate('Salecto_DataIntegrity::details_modal.phtml')
            ->setData(compact('test', 'issue'))
            ->toHtml();

        $resultJson = $this->resultJsonFactory->create();
        $result = [
            'success' => true,
            'modal_content' => $readmeHtml
        ];

        return $resultJson->setData($result);
    }
}
