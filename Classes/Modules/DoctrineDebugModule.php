<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;

use Doctrine\DBAL\Logging\DebugStack;
use TYPO3\CMS\Adminpanel\Modules\AbstractModule;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class DoctrineDebugModule extends AbstractModule
{

    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'admext_doctrinedebug';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'SQL Log';
    }

    public function getIconIdentifier(): string
    {
        return 'actions-database';
    }

    public function getShortInfo(): string
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $logger = $connection->getConfiguration()->getSQLLogger();
        if (null !== $logger) {
            $queryCount = \count($logger->queries);
            return '(' . $queryCount . ' queries)';
        }
        return '';
    }
}