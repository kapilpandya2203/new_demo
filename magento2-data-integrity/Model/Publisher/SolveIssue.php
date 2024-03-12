<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Model\Publisher;

use InvalidArgumentException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Salecto\DataIntegrity\Api\Data\IntegrityIssuesInterface;

/**
 *
 */
class SolveIssue
{
    public const TOPIC_NAME = 'dataintegrity.issues.solve';
    protected PublisherInterface $publisher;

    /**
     * @param PublisherInterface $publisher
     */
    public function __construct(
        PublisherInterface $publisher
    )
    {
        $this->publisher = $publisher;
    }

    /**
     * @param IntegrityIssuesInterface $issue
     * @throws InvalidArgumentException
     */
    public function publish(IntegrityIssuesInterface $issue)
    {
        $this->publisher->publish(self::TOPIC_NAME, $issue);
    }
}
