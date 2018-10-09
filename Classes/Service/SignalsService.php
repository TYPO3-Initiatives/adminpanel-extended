<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;


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
