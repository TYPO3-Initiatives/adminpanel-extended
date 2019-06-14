<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Log\LogRecord;

class SignalsService
{
    public function getSignalDataFromLogRecord(LogRecord $logRecord): array
    {
        $responseData = $logRecord->getData();
        $signalArguments = $responseData['signalArguments'] ?? [];
        foreach ($signalArguments as $key => $datum) {
            if (\is_object($datum)) {
                $signalArguments[$key] = \get_class($datum);
            }
        }
        return $signalArguments;
    }
}
