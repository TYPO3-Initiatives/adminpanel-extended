<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Hook;

use Doctrine\DBAL\Logging\DebugStack;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseConnectionHook
 */
final class DatabaseConnectionHook
{
    /**
     * Add a logger to the default connection
     *
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function modifyConnection(): void
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $connection->getConfiguration()->setSQLLogger(new DebugStack());
    }
}
