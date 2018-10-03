<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;


use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Adminpanel\Log\InMemoryLogWriter;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\InitializableInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleInterface;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class Hooks implements ModuleInterface, ContentProviderInterface, DataProviderInterface, InitializableInterface
{

    /**
     * Sub-Module content as rendered HTML
     *
     * @param \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData $moduleData
     * @return string
     */
    public function getContent(ModuleData $moduleData): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Private/Templates/Hooks.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));

        $view->assign('entries', $moduleData['hooks']);
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
        return 'debug_hooks';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_debug.xlf:submodule.hooks.label'
        );
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    public function getDataToStore(ServerRequestInterface $request): ModuleData
    {
        $log = InMemoryLogWriter::$log;
        $entries = array_filter(
            $log,
            function (LogRecord $entry) {
                return $entry->getComponent() === 'Psychomieze.AdminpanelExtended.Modules.HooksAndSignals.LoggedArray';
            }
        );
        return new ModuleData(
            [
                'hooks' => $entries,
            ]
        );
    }

    /**
     * Initialize the module - runs early in a TYPO3 request
     *
     * @param ServerRequestInterface $request
     */
    public function initializeModule(ServerRequestInterface $request): void
    {
        // overwrite SC_OPTIONS to track calls - ugly, but there's no other registry for hooks
        $scOptions = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'];
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'] = new DummyFirstLevelArrayObject(
            $scOptions
        );
    }

    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}