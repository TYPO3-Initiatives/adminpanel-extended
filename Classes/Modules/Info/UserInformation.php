<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Info;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use Psychomieze\AdminpanelExtended\Domain\Repository\UserSessionRepository;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ResourceProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class UserInformation
 */
class UserInformation extends AbstractSubModule
    implements DataProviderInterface, ContentProviderInterface, ResourceProviderInterface
{

    private $frontendUserSessionRepository;
    /**
     * @var \Psychomieze\AdminpanelExtended\Domain\Repository\UserSessionRepository
     */
    private $backendUserSessionRepository;

    public function __construct(
        UserSessionRepository $frontendUserSessionRepository = null,
        UserSessionRepository $backendUserSessionRepository = null
    ) {
        $this->frontendUserSessionRepository = $frontendUserSessionRepository
                                               ??
                                               GeneralUtility::makeInstance(
                                                   UserSessionRepository::class,
                                                   UserSessionRepository::CONTEXT_FE
                                               );
        $this->backendUserSessionRepository = $backendUserSessionRepository
                                              ??
                                              GeneralUtility::makeInstance(
                                                  UserSessionRepository::class,
                                                  UserSessionRepository::CONTEXT_BE
                                              );
    }

    /**
     * Identifier for this Sub-module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'info_userinformation';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_info.xlf:submodule.userinformation.label'
        );
    }

    /**
     * Sub-Module content as rendered HTML
     *
     * @return string
     */
    public function getContent(ModuleData $moduleData): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Private/Templates/Info/UserInformation.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));

        $view->assignMultiple($moduleData->getArrayCopy());

        return $view->render();
    }

    /**
     * Prepare module data
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    public function getDataToStore(ServerRequestInterface $request): ModuleData
    {
        return new ModuleData(
            [
                'isPageBeingEdited' => $this->isPageLocked(),
                'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
                'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
                'onlineFrontendUsers' => $this->findAllActiveFrontendUsers(),
                'onlineBackendUsers' => $this->findAllActiveBackendUsers(),
            ]
        );
    }

    /**
     * Returns a string array with javascript files that will be rendered after the module
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.js'];
     *
     * @return array
     */
    public function getJavaScriptFiles(): array
    {
        return [];
    }

    /**
     * Returns a string array with css files that will be rendered after the module
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.css'];
     *
     * @return array
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:adminpanel_extended/Resources/Public/Css/Messages.css',
        ];
    }

    /**
     * Find all fe user sessions and return the total.
     *
     * @return int
     */
    protected function findAllActiveFrontendUsers(): int
    {
        return \count($this->frontendUserSessionRepository->findAllActive());
    }

    /**
     * Find all be user sessions and return the total.
     *
     * @return int
     */
    protected function findAllActiveBackendUsers(): int
    {
        return \count($this->backendUserSessionRepository->findAllActive());
    }

    /**
     * Returns true if someone else is editing the current page. Otherwise returns false.
     *
     * @codeCoverageIgnore
     * @return bool
     */
    protected function isPageLocked(): bool
    {
        return (bool)BackendUtility::isRecordLocked('pages', $GLOBALS['TSFE']->id);
    }
}
