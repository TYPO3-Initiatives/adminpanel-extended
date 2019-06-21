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

        $message = $templateName;


        if (StringUtility::beginsWith($message, Environment::getExtensionsPath())) {
            $message = str_replace(Environment::getExtensionsPath() . DIRECTORY_SEPARATOR, 'EXT:', $message);
        } elseif (StringUtility::beginsWith($message, Environment::getFrameworkBasePath())) {
            $message = str_replace(Environment::getFrameworkBasePath() . DIRECTORY_SEPARATOR, 'EXT:', $message);
        }


        $this->logger->log(
            LogLevel::DEBUG,
            $message,
            [
                'controller' => $controller,
                'action' => $action,
                'format' => $format ?? $this->getFormat()
            ]
        );

        return $templateName;
    }
}
