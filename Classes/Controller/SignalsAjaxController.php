<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Controller;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals;
use Psychomieze\AdminpanelExtended\Service\ModuleDataService;
use Psychomieze\AdminpanelExtended\Service\SignalsService;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SignalsAjaxController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var \Psychomieze\AdminpanelExtended\Service\SignalsService
     */
    private $signalsService;

    private $moduleDataService;

    public function __construct(SignalsService $signalsService = null, ModuleDataService $moduleDataService = null)
    {
        $this->signalsService = $signalsService ?? GeneralUtility::makeInstance(SignalsService::class);
        $this->moduleDataService = $moduleDataService ?? GeneralUtility::makeInstance(ModuleDataService::class);
    }

    public function getData(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $signalId = (string)($queryParams['signalId'] ?? '');
        $requestId = (string)($queryParams['requestId'] ?? '');
        $this->validateParameters($signalId, $requestId);
        $moduleData = $this->moduleDataService->getModuleDataByRequestId(Signals::class, $requestId);
        $signalData = [];
        $statusCode = 404;
        if ($moduleData instanceof ModuleData) {
            $logRecord = $moduleData['signals'][$signalId] ?? null;
            if ($logRecord instanceof LogRecord) {
                $signalData['data'] = $this->signalsService->getSignalDataFromLogRecord(
                    $logRecord
                );
                $signalData['signalId'] = $signalId;
                $statusCode = 200;
            }
        }
        return new JsonResponse($signalData, $statusCode);
    }


    private function validateParameters(string $signalId, string $requestId): void
    {
        if (!$signalId || !$requestId) {
            throw new \InvalidArgumentException(
                'Missing parameters, signalId and requestId need to be set.', 1539121176
            );
        }
    }
}
