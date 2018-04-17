<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\DoctrineDebug;

use TYPO3\CMS\Adminpanel\Modules\AdminPanelSubModuleInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class QueryInformation implements AdminPanelSubModuleInterface
{

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
            if(is_array($query['params'])) {
                foreach($query['params'] as $k => $param) {
                    if (is_array($param)) {
                        $query['params'][$k] = implode(',', $param);
                    }
                }
            }
            if (isset($groupedQueries[$identifier])) {
                $groupedQueries[$identifier]['count']++;
                $groupedQueries[$identifier]['time'] += $query['executionMS'];
                $groupedQueries[$identifier]['queries'][] = $query;
            } else {
                $groupedQueries[$identifier] = [
                    'sql' => $query['sql'],
                    'time' => $query['executionMS'],
                    'count' => 1,
                    'queries' => [
                        $query
                    ]
                ];
            }
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
     * Identifier for this Sub-module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'doctrine-queryinfo';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Queries';
    }

    /**
     * Settings as HTML form elements (without wrapping form tag or save button)
     *
     * @return string
     */
    public function getSettings(): string
    {
        return '';
    }

    /**
     * Initialize the module - runs early in a TYPO3 request
     *
     * @param \TYPO3\CMS\Core\Http\ServerRequest $request
     */
    public function initializeModule(ServerRequest $request): void
    {
        // TODO: Implement initializeModule() method.
    }


    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}