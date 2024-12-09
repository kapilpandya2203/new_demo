<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use Magento\Framework\Phrase;
use ReflectionException;
use Exception;

abstract class AbstractTest
{
    /**
     * @var SolutionInterface[]
     */
    protected array $solutions;
    protected string $name;
    protected string $code;
    protected string $description;
    protected TestDependencyManager $dependencyManager;

    public function __construct(
        array                 $solutions,
        TestDependencyManager $dependencyManager
    )
    {
        $this->solutions = $solutions;
        $this->dependencyManager = $dependencyManager;
        $this->description = '';
        $this->configure();
    }

    public function configure()
    {
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setDescriptionTemplate($description): void
    {
        $this->description = $description;
    }

    public function getDescriptionTemplate(): string
    {
        return $this->description;
    }

    public function getSolutions(): array
    {
        return $this->solutions;
    }

    public function getSolutionByCode(string $code)
    {
        $solutions = $this->getSolutions();
        foreach ($solutions as $solution) {
            if ($solution->getCode() === $code) {
                return $solution;
            }
        }

        return false;
    }

    /**
     * This generates the full description form the description template
     * using data
     * @param $data
     * @return string
     */
    protected function generateFullDescription($data): string
    {
        $description = $this->getDescriptionTemplate();
        foreach ($data as $key => $value) {
            $description = str_replace("{{$key}}", $value, $description);
        }
        return $description;
    }

    /**
     * @param string $checksum
     * @return bool
     */
    protected function issueWithChecksumExists(string $checksum): bool
    {
        $issueCollection = $this->dependencyManager->getIssueCollectionFactory()->create();
        $issueCount = $issueCollection->addFieldToFilter('checksum', $checksum)
            ->count();
        return $issueCount > 0;
    }

    /**
     * @param $issueData array
     * @param array $descriptionData data used to generate the issue description
     * @return void
     */
    public function addIssue($issueData, array $descriptionData): void
    {
        $checksum = $this->generateChecksum($this->getCode() . $this->dependencyManager->getSerializer()->serialize($issueData));

        if (!$this->issueWithChecksumExists($checksum)) {
            $issue = $this->dependencyManager->getIssueModelFactory()->create();
            $issue->setTestName($this->getName())
                ->setTestCode($this->getCode())
                ->setDescription($this->generateFullDescription($descriptionData))
                ->setIssueData($issueData)
                ->setChecksum($checksum)
                ->setStatus('detected');

            $this->dependencyManager->getIssueRepository()->save($issue);
        }
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateChecksum($data): string
    {
        return hash('sha256', $data);
    }

    /**
     * Gets the readme for the test in HTML format
     * @return string
     * @throws ReflectionException
     */
    public function getReadmeInHtml(): string
    {
        return $this->dependencyManager->getReadmeReader()->getReadmeInHtml(static::class);
    }

    /**
     * Logs the error message along with extra common information
     * @param Exception $exception
     * @param string|null $testCode
     * @param Phrase|null $message
     * @return void
     */
    public function logError(Exception $exception, ?string $testCode, ?Phrase $message = null): void
    {
        if ($message === null) {
            $message = __();
        }

        if ($testCode !== null) {
            $message .= ' ' . __('Error occurred for test with code: %1.', $testCode);
        }

        $message .= ' ' . __('Error message: %1.', $exception->getMessage());

        $this->dependencyManager->getLogger()->error($message);
    }
}
