<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class DummyFirstLevelArrayObject extends \ArrayObject
{
    public function offsetExists($index)
    {
        $secondLevel = null;
        // do NOT use isset or alike here, as that will call offsetExists in an endless loop
        if (
            \array_key_exists('TYPO3_CONF_VARS', $GLOBALS) &&
            \array_key_exists('SC_OPTIONS', $GLOBALS['TYPO3_CONF_VARS']) &&
            \array_key_exists($index, $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'])
        ) {
            $secondLevel = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$index];
        }
        if (\is_array($secondLevel)) {
            $loggedArray = new LoggedArray($secondLevel);
            $loggedArray->setParent('$GLOBALS[\'TYPO3_CONF_VARS\'][\'SC_OPTIONS\'][\'' . $index . '\']');
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$index] = $loggedArray;
            return true;
        }

        return parent::offsetExists($index);
    }
}
