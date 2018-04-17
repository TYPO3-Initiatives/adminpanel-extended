<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Hooks;

use Doctrine\DBAL\Logging\DebugStack;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DoctrineDebugHook
{
    /**
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