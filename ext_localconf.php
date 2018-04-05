<?php
defined('TYPO3_MODE') or die('Access denied.');
if (TYPO3_MODE === 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB'][] = \Psychomieze\AdminpanelExtended\Modules\DoctrineDebugModule::class .
                                                                                             '->modifyConnection';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['adminpanel']['modules']['admext_doctrinedebug'] = [
        'module' => \Psychomieze\AdminpanelExtended\Modules\DoctrineDebugModule::class,
    ];
}
