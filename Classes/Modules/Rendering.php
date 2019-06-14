<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;


use TYPO3\CMS\Adminpanel\ModuleApi\AbstractModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ShortInfoProviderInterface;

class Rendering extends AbstractModule implements ShortInfoProviderInterface
{
    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'psychomieze_rendering';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_rendering.xlf:module.rendering.label'
        );
    }

    /**
     * Displayed directly in the bar
     *
     * @return string
     */
    public function getShortInfo(): string
    {
        return '';
    }

    /**
     * Icon identifier - needs to be registered in iconRegistry
     *
     * @return string
     */
    public function getIconIdentifier(): string
    {
        return 'actions-document';
    }
}
