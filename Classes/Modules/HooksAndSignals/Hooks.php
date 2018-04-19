<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;


use Psychomieze\AdminpanelExtended\Logger\Log;
use TYPO3\CMS\Adminpanel\Modules\AdminPanelSubModuleInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class Hooks implements AdminPanelSubModuleInterface
{

    /**
     * Sub-Module content as rendered HTML
     *
     * @return string
     */
    public function getContent(): string
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cache = $cacheManager->getCache('cache_runtime');
        $backend = $cache->getBackend();
        $entryKeys = $backend->findIdentifiersByTag('adminpanel');
        $entries = [];
        foreach ($entryKeys ?? [] as $entry) {
            $entries[] = $cache->get($entry);
        }
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templateNameAndPath = 'EXT:adminpanel_extended/Resources/Templates/HooksAndSignals/Hooks.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));

        $view->assignMultiple(
            [
                'entries' => $entries,
            ]
        );

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
        return 'hs-hooks';
    }

    /**
     * Sub-Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Hooks';
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
}