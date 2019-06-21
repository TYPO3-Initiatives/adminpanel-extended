<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Fluid;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class TemplatePaths extends \TYPO3\CMS\Fluid\View\TemplatePaths
{
    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    private $logger;

    public function __construct($packageNameOrArray = null)
    {
        parent::__construct($packageNameOrArray);

        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    public function resolveTemplateFileForControllerAndActionAndFormat($controller, $action, $format = null): ?string
    {
        $templateName = parent::resolveTemplateFileForControllerAndActionAndFormat(
            $controller,
            $action,
            $format
        );

        if (StringUtility::beginsWith($templateName, Environment::getExtensionsPath())) {
            $path = str_replace(Environment::getExtensionsPath() . DIRECTORY_SEPARATOR, 'EXT:', $templateName);
        } elseif (StringUtility::beginsWith($templateName, Environment::getFrameworkBasePath())) {
            $path = str_replace(Environment::getFrameworkBasePath() . DIRECTORY_SEPARATOR, 'EXT:', $templateName);
        }

        $format = $format ?? $this->getFormat();
        $identifier = uniqid("template-{$controller}-{$action}-{$format}-", false);

        $this->logger->log(
            LogLevel::DEBUG,
            $identifier,
            [
                'path' => $path ?? $templateName,
                'controller' => $controller,
                'action' => $action,
                'format' => $format
            ]
        );

        return $templateName;
    }
}
