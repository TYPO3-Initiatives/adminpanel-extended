<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Info;

use Psychomieze\AdminpanelExtended\Domain\Repository\FrontendUserSessionRepository;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class UserInformation
 */
class UserInformation implements ModuleInterface, ContentProviderInterface
{
    /**
     * Sub-Module content as rendered HTML
     *
     * @return string
     */
    public function getContent(ModuleData $moduleData): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Templates/Info/UserInformation.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));

        $view->assignMultiple([
            'isPageBeingEdited' => $this->isPageLocked(),
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
     * Find all fe user sessions and return the total.
     *
     * @return int
     */
    protected function findAllActiveFrontendUsers(): int
    {
        $frontendUserSessionReposiotry = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(FrontendUserSessionRepository::class);

        return \count($frontendUserSessionReposiotry->findAllActive());
    }

    /**
     * Find all be user sessions and return the total.
     *
     * @return int
     */
    protected function findAllActiveBackendUsers(): int
    {
        $backendUserSessionRepository = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(BackendUserSessionRepository::class);

        return \count($backendUserSessionRepository->findAllActive());
    }

    /**
     * Returns true if someone else is editing the current page. Otherwise returns false.
     *
     * @return bool
     */
    protected function isPageLocked(): bool
    {
        return (bool)BackendUtility::isRecordLocked('pages', $GLOBALS['TSFE']->id);
    }
}
