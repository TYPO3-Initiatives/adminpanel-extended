<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;

use ArrayObject;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoggedArray extends ArrayObject
{
    protected $parent = '';

    public function setParent(string $parent): void
    {
        $this->parent = $parent;
    }

    public function offsetExists($index)
    {
        $hook = $this->parent . '[\'' . $index . '\']';
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        array_shift($backtrace);
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger(__CLASS__);
        $logger->log(LogLevel::DEBUG, $hook, ['hook' => $hook, 'backtrace' => $backtrace]);
        if (parent::offsetExists($index)) {
            return true;
        }
        return false;
    }
}