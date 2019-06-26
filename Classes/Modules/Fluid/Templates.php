<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Adminpanel\Log\InMemoryLogWriter;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ResourceProviderInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Fluid templates submodule
 */
class Templates extends AbstractSubModule implements ContentProviderInterface, DataProviderInterface, ResourceProviderInterface
{
    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'psychomieze_fluid_templates';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_fluid.xlf:submodule.templates.label'
        );
    }

    /**
     * Main method for content generation of an admin panel module.
     * Return content as HTML. For modules implementing the DataProviderInterface
     * the "ModuleData" object is automatically filled with the stored data - if
     * no data is given a "fresh" ModuleData object is injected.
     *
     * @param \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData $data
     * @return string
     */
    public function getContent(ModuleData $data): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);

        $view->setTemplatePathAndFilename('EXT:adminpanel_extended/Resources/Private/Templates/Fluid/Templates.html');
        $view->assignMultiple($data->getArrayCopy());
        $url = GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute(
            'ajax_adminPanelExtended_templateData',
            ['requestId' => $data['requestId']]
        );
        $view->assign('templateDataUri', $url);

        return $view->render();
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    public function getDataToStore(ServerRequestInterface $request): ModuleData
    {
        $log = InMemoryLogWriter::$log;
        /** @var LogRecord[] $templateRecords */
        $templateRecords = array_filter(
            $log,
            static function (LogRecord $entry) {
                return $entry->getComponent() === 'Psychomieze.AdminpanelExtended.Modules.Fluid.TemplatePaths';
            }
        );

        $templates = [];

        foreach ($templateRecords as $logRecord) {
            $templates[$logRecord->getMessage()] = $logRecord->getData();
        }

        $templates = array_unique($templates, SORT_REGULAR);

        return new ModuleData([
            'templates' => $templates,
            'requestId' => $request->getAttribute('adminPanelRequestId')
        ]);
    }

    /**
     * Returns a string array with javascript files that will be rendered after the module
     *
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.js'];
     *
     * @return array
     */
    public function getJavaScriptFiles(): array
    {
        return [
            'EXT:adminpanel_extended/Resources/Public/JavaScript/Templates.js',
            'EXT:adminpanel_extended/Resources/Public/JavaScript/vendor/prettify.js',
        ];
    }

    /**
     * Returns a string array with css files that will be rendered after the module
     *
     * Example: return ['EXT:adminpanel/Resources/Public/JavaScript/Modules/Edit.css'];
     *
     * @return array
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:adminpanel_extended/Resources/Public/Css/Templates.css',
            'EXT:adminpanel_extended/Resources/Public/Css/vendor/desert.css'
        ];
    }
}
