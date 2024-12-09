<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model;

use Exception;
use Magento\Framework\Phrase;
use ReflectionException;

abstract class AbstractSolution
{
    protected string $name = 'Default';
    protected string $code = 'default';

    protected SolutionDependencyManager $dependencyManager;

    public function __construct(
        SolutionDependencyManager $dependencyManager
    )
    {
        $this->dependencyManager = $dependencyManager;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
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
     * @param int|null $issueId
     * @param Phrase|null $message
     * @return void
     */
    public function logError(Exception $exception, ?int $issueId, ?Phrase $message = null): void
    {
        if ($message === null) {
            $message = __();
        }

        if ($issueId !== null) {
            $message .= ' ' . __('Error occurred for issue with ID: %1.', $issueId);
        }

        $message .= ' ' . __('Error message: %1.', $exception->getMessage());

        $this->dependencyManager->getLogger()->error($message);
    }
}
