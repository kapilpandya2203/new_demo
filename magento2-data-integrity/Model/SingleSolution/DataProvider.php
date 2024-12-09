<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model\SingleSolution;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as Provider;

class DataProvider extends Provider
{
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $issueId = $this->request->getParam('issue_id');
        return [
            'items' => [
                [
                    'issue_id' => $issueId
                ]
            ]
        ];
    }

    /**
     * Get Meta data
     *
     * @return array
     */
    public function getMeta()
    {
        return parent::getMeta();
    }

}
