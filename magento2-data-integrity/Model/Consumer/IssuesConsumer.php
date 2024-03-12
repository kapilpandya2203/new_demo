<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model\Consumer;

use Psr\Log\LoggerInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;
use Salecto\DataIntegrity\Model\AbstractTest;
use Salecto\DataIntegrity\Model\IntegrityIssuesRepository;
use Salecto\DataIntegrity\Model\IntegrityTests;

class IssuesConsumer
{
    protected LoggerInterface $logger;
    protected IntegrityIssuesRepository $issueRepository;
    protected IntegrityTests $integrityTestsModel;

    /**
     * Consumer constructor.
     * @param LoggerInterface $logger
     * @param IntegrityIssuesRepository $issuesRepository
     * @param IntegrityTests $integrityTestsModel
     */
    public function __construct(
        LoggerInterface           $logger,
        IntegrityIssuesRepository $issuesRepository,
        IntegrityTests            $integrityTestsModel
    )
    {
        $this->logger = $logger;
        $this->issueRepository = $issuesRepository;
        $this->integrityTestsModel = $integrityTestsModel;
    }

    public function process(IntegrityIssuesInterface $data)
    {
        $hasProcessSucceeded = false;
        try {
            $issueId = $data->getId();
            $issue = $this->issueRepository->getById($issueId);
            if ($issue) {
                $testCode = $issue->getTestCode();
                /** @var AbstractTest $test */
                $test = $this->integrityTestsModel->getTestByCode($testCode);
                if ($test) {
                    $selectedSolution = $issue->getSelectedSolution();
                    $solution = $test->getSolutionByCode($selectedSolution);
                    if ($solution) {
                        $solution->execute($issue);
                        $hasProcessSucceeded = true;
                    } else {
                        $this->logger->error(__('Could not find the solution %1 in the test %2', $selectedSolution, $testCode));
                    }
                } else {
                    $this->logger->error(__('Could not find the test with code %1', $testCode));
                }
            } else {
                $this->logger->error(__('Could not find the issue with ID %1', $issueId));
            }
        } catch (\Exception $exception) {
            $this->logger->error(__('Issue consumer failed with error: %1', $exception->getMessage()));
        }


        return $hasProcessSucceeded;
    }
}
