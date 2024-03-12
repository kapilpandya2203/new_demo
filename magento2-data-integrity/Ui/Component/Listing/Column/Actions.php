<?php

namespace Salecto\DataIntegrity\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['issue_id'])) {
                    $itemActions = [
                        'details' => [
                            'callback' => [
                                [
                                    'provider' => 'issue_listing.issue_listing.issue_detail',
                                    'target' => 'updateAndOpen',
                                    'params' => [
                                        'issue_id' => $item['issue_id'],
                                        'action' => $this->urlBuilder->getUrl('data_integrity/issues/details', ['issue' => $item['issue_id']])
                                    ],
                                ]
                            ],
                            'href' => '#',
                            'label' => __('Details')
                        ],
                    ];
                    if ($item['status'] == 'detected') {
                        $itemActions['fix'] = [
                            'callback' => [
                                [
                                    'provider' => 'issue_listing.issue_listing.solution_form_modal.solution_form_loader',
                                    'target' => 'destroyInserted',
                                ],
                                [
                                    'provider' => 'issue_listing.issue_listing.solution_form_modal',
                                    'target' => 'openModal',
                                    'params' => [
                                        'issue_id' => $item['issue_id'],
                                        'test_code' => $item['test_code']
                                    ]
                                ],
                                [
                                    'provider' => 'issue_listing.issue_listing.solution_form_modal.solution_form_loader',
                                    'target' => 'render',
                                    'params' => [
                                        'issue_id' => $item['issue_id'],
                                        'test_code' => $item['test_code']
                                    ]
                                ]
                            ],
                            'href' => '#',
                            'label' => __('Fix')
                        ];
                    }
                    $item[$this->getData('name')] = $itemActions;
                }
            }
        }

        return $dataSource;
    }
}
