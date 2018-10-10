<?php
/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Definitions for routes provided by EXT:adminpanelExtended
 */
return [
    'adminPanelExtended_signalData' => [
        'path' => '/adminpanelExtended/signals/data',
        'target' => \Psychomieze\AdminpanelExtended\Controller\SignalsAjaxController::class . '::getData'
    ],
];
