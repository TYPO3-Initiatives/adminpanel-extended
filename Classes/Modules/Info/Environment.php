<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Info;

use Psychomieze\AdminpanelExtended\Service\SystemInformationInterface;
use Psychomieze\AdminpanelExtended\Service\SystemInformationService;
use TYPO3\CMS\Adminpanel\Modules\AdminPanelSubModuleInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class Environment
 */
class Environment implements AdminPanelSubModuleInterface
{
    /**
     * @return string
     */
    public function getContent(): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Templates/Info/Environment.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));


        $view->assignMultiple([
            'systemEnvironmentInformation' => $this->getSystemInformationService()->collectInformation()
        ]);

        return $view->render();
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'info-environment';
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return 'Environment';
    }

    /**
     * @return string
     */
    public function getSettings(): string
    {
        return '';
    }

    /**
     * @param \TYPO3\CMS\Core\Http\ServerRequest $request
     */
    public function initializeModule(ServerRequest $request): void
    {
        // TODO: Implement initializeModule() method.
    }

    /**
     * @return \Psychomieze\AdminpanelExtended\Service\SystemInformationInterface
     */
    protected function getSystemInformationService(): SystemInformationInterface
    {
        return GeneralUtility::makeInstance(SystemInformationService::class);
    }
}
