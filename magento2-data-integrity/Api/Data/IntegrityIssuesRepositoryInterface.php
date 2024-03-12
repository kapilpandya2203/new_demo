<?php
declare(strict_types=1);

namespace Salecto\DataIntegrity\Api\Data;

use DateTime;

interface IntegrityIssuesRepositoryInterface
{
    /**
     * @param int $issueId
     * @return IntegrityIssuesInterface
     */
    public function getById($issueId);

    /**
     * @param string $issueCode
     * @return IntegrityIssuesInterface
     */
    public function getByCode($issueCode);

    /**
     * @param IntegrityIssuesInterface $issue
     * @return IntegrityIssuesInterface
     */
    public function save(IntegrityIssuesInterface $issue);

    /**
     * @param IntegrityIssuesInterface $issue
     * @return bool
     */
    public function delete(IntegrityIssuesInterface $issue);

    /**
     * @param int $issueId
     * @return bool
     */
    public function deleteById($issueId);

    /**
     * @param DateTime $date
     * @return bool
     */
    public function deleteIssuesOlderThanDate(DateTime $date);
}
