<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Adminpanel\ModuleApi\AbstractModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ShortInfoProviderInterface;

class Fluid extends AbstractModule implements ShortInfoProviderInterface
{
    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'psychomieze_fluid';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_fluid.xlf:module.fluid.label'
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
        return 'actions-document'; //only used as long as we don't have a better one.
    }
}
