<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;


class DummyFirstLevelArrayObject extends \ArrayObject
{
    public function offsetExists($index)
    {
        if (parent::offsetExists($index)) {
            $secondLevel = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$index];
            if (is_array($secondLevel)) {
                $loggedArray = new LoggedArray($secondLevel);
                $loggedArray->setParent('$GLOBALS[\'TYPO3_CONF_VARS\'][\'SC_OPTIONS\'][\'' . $index . '\']');
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$index] = $loggedArray;
            }
            return true;
        }
        return false;
    }
}