<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Adminpanel\Log\InMemoryLogWriter;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ResourceProviderInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class Signals implements ModuleInterface, ContentProviderInterface, DataProviderInterface, ResourceProviderInterface
{

    /**
     * Sub-Module content as rendered HTML
     *
     * @codeCoverageIgnore FE Rendering
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function getContent(ModuleData $moduleData): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uri = $uriBuilder->buildUriFromRoute(
            'ajax_adminPanelExtended_signalData',
            [
                'requestId' => $moduleData['requestId']
            ]
        );
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Private/Templates/Debug/Signals.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));
        $view->setPartialRootPaths(
            [
                'EXT:adminpanel/Resources/Private/Partials',
                'EXT:adminpanel_extended/Resources/Private/Partials',
            ]
        );

        $view->assign('signals', $moduleData['signals'])
            ->assign('signalDataUri', $uri);

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
        return 'psychomieze_debug_signals';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_debug.xlf:submodule.signals.label'
        );
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    /**
     * @inheritdoc
     */
    public function getDataToStore(ServerRequestInterface $request): ModuleData
    {
        $log = InMemoryLogWriter::$log;
        $signals = array_filter(
            $log,
            function (LogRecord $entry) {
                return $entry->getComponent() === 'TYPO3.CMS.Extbase.SignalSlot.Dispatcher';
            }
        );
        $entries = [];
        foreach ($signals as $signal) {
            $entries[uniqid('signal-', false)] = $signal;
        }
        return new ModuleData(
            [
                'signals' => $entries,
                'requestId' => $request->getAttribute('adminPanelRequestId')
            ]
        );
    }

    /**
     * Returns a string array with javascript files that will be rendered after the module
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.js'];
     *
     * @codeCoverageIgnore Configuration
     * @return array
     */
    public function getJavaScriptFiles(): array
    {
        return [
            'EXT:adminpanel_extended/Resources/Public/JavaScript/Signals.js'
        ];
    }

    /**
     * Returns a string array with css files that will be rendered after the module
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.css'];
     *
     * @codeCoverageIgnore Configuration
     * @return array
     */
    public function getCssFiles(): array
    {
        return [];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
