<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Controller;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psychomieze\AdminpanelExtended\Modules\Fluid\Templates;
use Psychomieze\AdminpanelExtended\Service\ModuleDataService;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TemplatesAjaxController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getData(ServerRequestInterface $request): JsonResponse
    {
        $queryParams = $request->getQueryParams();
        $templateId = (string)($queryParams['templateId'] ?? '');
        $requestId = (string)($queryParams['requestId'] ?? '');
        $this->validateParameters($templateId, $requestId);
        $moduleData = GeneralUtility::makeInstance(ModuleDataService::class)
            ->getModuleDataByRequestId(Templates::class, $requestId);
        $templateData = [];
        $statusCode = 404;

        if ($moduleData instanceof ModuleData) {
            $templateRecord = $moduleData['templates'][$templateId] ?? null;
            if (is_array($templateRecord)) {
                $absTemplatePath = GeneralUtility::getFileAbsFileName($templateRecord['path']);
                if (GeneralUtility::isAllowedAbsPath($absTemplatePath) && file_exists($absTemplatePath)) {
                    $content = file_get_contents($absTemplatePath);
                    $statusCode = 200;
                    $templateData['templateId'] = $templateId;
                    $templateData['template'] = $content;
                }
            }
        }

        return new JsonResponse($templateData, $statusCode);
    }

    private function validateParameters(string $templateId, string $requestId): void
    {
        if (!$templateId || !$requestId) {
            throw new \InvalidArgumentException(
                'Missing parameters, templateId and requestId need to be set.',
                1561386190
            );
        }
    }
}
