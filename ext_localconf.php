<?php
defined('TYPO3_MODE') or die('Access denied.');
if (TYPO3_MODE === 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB'][] = \Psychomieze\AdminpanelExtended\Hooks\DoctrineDebugHook::class .
                                                                                             '->modifyConnection';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['adminpanel']['modules']['admext_doctrinedebug'] = [
        'module' => \Psychomieze\AdminpanelExtended\Modules\DoctrineDebugModule::class,
        'submodules' => [
            'query-info' => [
                'module' => \Psychomieze\AdminpanelExtended\Modules\DoctrineDebug\QueryInformation::class,
            ],
        ],
    ];
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['adminpanel']['modules']['admext_hs'] = [
        'module' => \Psychomieze\AdminpanelExtended\Modules\HooksAndSignalsModule::class,
        'submodules' => [
            'hooks' => [
                'module' => \Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Hooks::class,
            ],
            'signals' => [
                'module' => \Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals::class,
            ]
        ],
    ];

    $scOptions = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'];
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'] = new \Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\DummyFirstLevelArrayObject(
        $scOptions
    );

}
$GLOBALS['TYPO3_CONF_VARS']['LOG']['adminpanel'] = [
    'writerConfiguration' => [
        \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
            \Psychomieze\AdminpanelExtended\Logger\RuntimeCacheWriter::class => [],
        ],
    ],
];

$GLOBALS['TYPO3_CONF_VARS']['LOG']['TYPO3']['CMS']['Extbase']['SignalSlot']['writerConfiguration'] = [
    \TYPO3\CMS\Core\Log\LogLevel::DEBUG => [
        \Psychomieze\AdminpanelExtended\Logger\RuntimeCacheWriter::class => [],
    ],
];