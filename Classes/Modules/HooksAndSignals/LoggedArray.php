<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;

use ArrayObject;
use Psychomieze\AdminpanelExtended\Logger\Log;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoggedArray extends ArrayObject
{
    protected $parent = '';

    public function setParent(string $parent)
    {
        $this->parent = $parent;
    }

    public function offsetExists($index)
    {
        $hook = $this->parent . '[\'' . $index . '\']';
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        array_shift($backtrace);
        $logManager = GeneralUtility::makeInstance(LogManager::class);
        $logger = $logManager->getLogger('adminpanel');
        $logger->log(LOG_DEBUG, $hook, ['hook' => $hook, 'backtrace' => $backtrace]);
        if (parent::offsetExists($index)) {
            return true;
        }
        return false;
    }
}