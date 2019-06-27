<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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

    /**
     * @param array|string|NULL $packageNameOrArray
     */
    public function __construct($packageNameOrArray = null)
    {
        parent::__construct($packageNameOrArray);

        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * Attempts to resolve an absolute filename
     * of a template (i.e. `templateRootPaths`)
     * using a controller name, action and format.
     *
     * Works _backwards_ through template paths in
     * order to achieve an "overlay"-type behavior
     * where the last paths added are the first to
     * be checked and the first path added acts as
     * fallback if no other paths have the file.
     *
     * If the file does not exist in any path,
     * including fallback path, `NULL` is returned.
     *
     * Path configurations filled from TypoScript
     * is automatically recorded in the right
     * order (see `fillFromTypoScriptArray`), but
     * when manually setting the paths that should
     * be checked, you as user must be aware of
     * this reverse behavior (which you should
     * already be, given that it is the same way
     * TypoScript path configurations work).
     *
     * @param string $controller
     * @param string $action
     * @param string $format
     * @return string|NULL
     * @api
     */
    public function resolveTemplateFileForControllerAndActionAndFormat($controller, $action, $format = null): ?string
    {
        $templateName = parent::resolveTemplateFileForControllerAndActionAndFormat(
            $controller,
            $action,
            $format
        );

        if (is_string($templateName)) {
            if (StringUtility::beginsWith($templateName, Environment::getExtensionsPath())) {
                $path = str_replace(Environment::getExtensionsPath().DIRECTORY_SEPARATOR, 'EXT:', $templateName);
            } elseif (StringUtility::beginsWith($templateName, Environment::getFrameworkBasePath())) {
                $path = str_replace(Environment::getFrameworkBasePath().DIRECTORY_SEPARATOR, 'EXT:', $templateName);
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
                    'format' => $format,
                ]
            );
        }

        return $templateName;
    }
}
