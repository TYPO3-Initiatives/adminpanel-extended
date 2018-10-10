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
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SignalsAjaxController
{

    public function getData(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $requestId = $queryParams['requestId'];
        $signalId = $queryParams['signalId'];
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cache = $cacheManager->getCache('adminpanel_requestcache');
        $data = $cache->get($requestId);
        foreach ($data ?? [] as $module) {
            if ($module instanceof  Signals) {
                break;
            }
        }
        $moduleData = $data->offsetGet($module);
        $logRecord = $moduleData['signals'][$signalId] ?? null;
        $signalData = [];
        $statusCode = 404;
        if($logRecord instanceof LogRecord) {
            $responseData = $logRecord->getData();
            $signalArguments = $responseData['signalArguments'];
            foreach ($signalArguments as $key => $datum) {
                if (\is_object($datum)) {
                    $signalArguments[$key] = \get_class($datum);
                }
            }
            $signalData['data'] = $signalArguments;
            $signalData['signalId'] = $signalId;
            $statusCode = 200;
        }
        return new JsonResponse($signalData, $statusCode);
    }
}
