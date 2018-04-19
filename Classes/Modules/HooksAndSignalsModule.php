<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;

use Doctrine\DBAL\Logging\DebugStack;
use TYPO3\CMS\Adminpanel\Modules\AbstractModule;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class HooksAndSignalsModule extends AbstractModule
{

    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'admext_hs';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return 'Hooks and signals';
    }

    public function getIconIdentifier(): string
    {
        return 'actions-file-csv';
    }

    public function getShortInfo(): string
    {
        return '';
    }
}