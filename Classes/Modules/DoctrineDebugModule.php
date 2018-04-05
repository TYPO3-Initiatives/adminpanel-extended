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
    private $queryCount = 0;

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

    /**
     * @return string Returns content of admin panel
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getContent(): string
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection = $connectionPool->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);
        $logger = $connection->getConfiguration()->getSQLLogger();
        $this->getLanguageService()->includeLLFile('EXT:adminpanel_extended/Resources/Language/locallang.xlf');
        if (null !== $logger) {
            $this->queryCount = \count($logger->queries);
            $groupedQueries = $this->groupQueries($logger->queries);
        }

        $view = new StandaloneView();
        $view->setTemplatePathAndFilename(
            'typo3conf/ext/adminpanel_extended/Resources/Templates/DoctrineDebug.html'
        );
        $view->assign('queries', $groupedQueries ?? []);
        return $view->render();

    }

    /**
     * @param array $queries
     * @return array
     */
    protected function groupQueries(array $queries): array
    {
        $groupedQueries = [];
        foreach ($queries as $query) {
            $identifier = sha1($query['sql']);
            $time = $groupedQueries[$identifier]['time'] ?? 0;
            $count = $groupedQueries[$identifier]['count'] ?? 0;
            $groupedQueries[$identifier] = [
                'sql' => $query['sql'],
                'time' => $time + $query['executionMS'],
                'count' => $count + 1,
            ];
        }
        uasort(
            $groupedQueries,
            function ($a, $b) {
                return $b['count'] <=> $a['count'];
            }
        );
        return $groupedQueries;
    }

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
        return 'SQL Log (' . $this->queryCount . ' queries)';
    }
}