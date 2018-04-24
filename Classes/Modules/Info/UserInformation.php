<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Info;

use TYPO3\CMS\Adminpanel\Modules\AdminPanelSubModuleInterface;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class UserInformation
 */
class UserInformation implements AdminPanelSubModuleInterface
{
    /**
     * Sub-Module content as rendered HTML
     *
     * @return string
     */
    public function getContent(): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Templates/Info/UserInformation.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));

        $view->assignMultiple([
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'onlineFrontendUsers' => $this->findAllActiveFrontendUsers(),
            'onlineBackendUsers' => $this->findAllActiveBackendUsers()
        ]);

        return $view->render();
    }

    /**
     * Identifier for this Sub-module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'info-userinformation';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Users';
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
    }

    /**
     * @return int
     */
    protected function findAllActiveFrontendUsers(): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_sessions');

        $onlineUsersCount = $queryBuilder->count('*')
            ->from('fe_sessions')
            ->groupBy('ses_userid')
            ->execute()
            ->fetchColumn();

        return (int)$onlineUsersCount;
    }

    /**
     * @return int
     */
    protected function findAllActiveBackendUsers(): int
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $backendUserSessionRepository = $objectManager->get(BackendUserSessionRepository::class);

        return count($backendUserSessionRepository->findAllActive());
    }
}
