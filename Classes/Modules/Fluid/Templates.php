<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Fluid templates submodule
 */
class Templates extends AbstractSubModule implements ContentProviderInterface
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

        return $view->render();
    }
}
