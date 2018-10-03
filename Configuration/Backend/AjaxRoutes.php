<?php

/**
 * Definitions for routes provided by EXT:adminpanelExtended
 */
return [
    'adminPanelExtended_signalData' => [
        'path' => '/adminpanelExtended/signals/data',
        'target' => \Psychomieze\AdminpanelExtended\Controller\SignalsAjaxController::class . '::getData'
    ],
];
