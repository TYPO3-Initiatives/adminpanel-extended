<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;

use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Extends original InfoModule with more functionality
 */
class InfoModule extends \TYPO3\CMS\Adminpanel\Modules\InfoModule
{
    /**
     * @var string
     */
    protected $extendedExtResources = 'EXT:adminpanel_extended/Resources';

    /**
     * Creates the content for the "info" section ("module") of the Admin Panel
     *
     * @return string HTML content for the section. Consists of a string with table-rows with four columns.
     * @see display()
     */
    public function getContent(): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $templateNameAndPath = $this->extendedExtResources . '/Templates/Info.html';
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templateNameAndPath));
        $view->setPartialRootPaths([$this->extResources . '/Partials']);
        $tsfe = $this->getTypoScriptFrontendController();

        $view->assignMultiple([
            'post' => $_POST,
            'get' => $_GET,
            'cookie' => $_COOKIE,
            'server' => $_SERVER,
            'info' => [
                'pageUid' => $tsfe->id,
                'pageType' => $tsfe->type,
                'groupList' => $tsfe->gr_list,
                'noCache' => $this->isNoCacheEnabled(),
                'countUserInt' => \count($tsfe->config['INTincScript'] ?? []),
                'totalParsetime' => $this->getTimeTracker()->getParseTime(),
                'feUser' => [
                    'uid' => $tsfe->fe_user->user['uid'] ?? 0,
                    'username' => $tsfe->fe_user->user['username'] ?? ''
                ],
                'imagesOnPage' => $this->collectImagesOnPage(),
                'documentSize' => $this->collectDocumentSize(),
                'systemEnvironmentInformation' => $this->collectInformation()
            ]
        ]);

        return $view->render();
    }

    /**
     * Collects images from TypoScriptFrontendController and calculates the total size.
     * Returns human readable image sizes for fluid template output
     *
     * @return array
     */
    protected function collectImagesOnPage(): array
    {
        $imagesOnPage = [
            'files' => [],
            'total' => 0,
            'totalSize' => 0,
            'totalSizeHuman' => GeneralUtility::formatSize(0)
        ];

        if ($this->isNoCacheEnabled() === false) {
            return $imagesOnPage;
        }

        $count = 0;
        $totalImageSize = 0;
        if (!empty($this->getTypoScriptFrontendController()->imagesOnPage)) {
            foreach ($this->getTypoScriptFrontendController()->imagesOnPage as $file) {
                $fileSize = @filesize($file);
                $imagesOnPage['files'][] = [
                    'name' => $file,
                    'size' => $fileSize,
                    'sizeHuman' => GeneralUtility::formatSize($fileSize)
                ];
                $totalImageSize += $fileSize;
                $count++;
            }
        }
        $imagesOnPage['totalSize'] = GeneralUtility::formatSize($totalImageSize);
        $imagesOnPage['total'] = $count;

        return $imagesOnPage;
    }

    /**
     * Gets the document size from the current page in a human readable format
     * @return string
     */
    protected function collectDocumentSize(): string
    {
        $documentSize = 0;
        if ($this->isNoCacheEnabled() === true) {
            $documentSize = \mb_strlen($this->getTypoScriptFrontendController()->content, 'UTF-8');
        }

        return GeneralUtility::formatSize($documentSize);
    }

    /**
     * @return bool
     */
    protected function isNoCacheEnabled(): bool
    {
        return (bool)$this->getTypoScriptFrontendController()->no_cache;
    }

    /**
     * Collect the system information
     */
    protected function collectInformation(): array
    {
        $systemInformation[] = $this->getTypo3Version();
        $systemInformation[] = $this->getWebServer();
        $systemInformation[] = $this->getPhpVersion();
        $systemInformation = $this->getDatabase($systemInformation);
        $systemInformation[] = $this->getApplicationContext();
        $systemInformation = $this->getComposerMode($systemInformation);
        $systemInformation = $this->getGitRevision($systemInformation);
        $systemInformation[] = $this->getOperatingSystem();

        return $systemInformation;
    }

    /**
     * Gets the TYPO3 version
     */
    protected function getTypo3Version(): array
    {
        return [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.typo3-version',
            'value' => VersionNumberUtility::getCurrentTypo3Version(),
            'iconIdentifier' => 'sysinfo-typo3-version'
        ];
    }

    /**
     * Gets the webserver software
     */
    protected function getWebServer(): array
    {
        return [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.webserver',
            'value' => $_SERVER['SERVER_SOFTWARE'],
            'iconIdentifier' => 'sysinfo-webserver'
        ];
    }

    /**
     * Gets the PHP version
     */
    protected function getPhpVersion(): array
    {
        return [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.phpversion',
            'value' => PHP_VERSION,
            'iconIdentifier' => 'sysinfo-php-version'
        ];
    }

    /**
     * Get the database info
     */
    protected function getDatabase(array $systemInformation): array
    {
        foreach (GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionNames() as $connectionName) {
            $systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.database',
                'titleAddition' => $connectionName,
                'value' => GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionByName($connectionName)
                    ->getServerVersion(),
                'iconIdentifier' => 'sysinfo-database'
            ];
        }

        return $systemInformation;
    }

    /**
     * Gets the application context
     */
    protected function getApplicationContext(): array
    {
        $applicationContext = GeneralUtility::getApplicationContext();
        return [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.applicationcontext',
            'value' => (string)$applicationContext,
            'status' => $applicationContext->isProduction() ? InformationStatus::STATUS_OK : InformationStatus::STATUS_WARNING,
            'iconIdentifier' => 'sysinfo-application-context'
        ];
    }

    /**
     * Adds the information if the Composer mode is enabled or disabled to the displayed system information
     */
    protected function getComposerMode(array $systemInformation): array
    {
        if (Environment::isComposerMode()) {
            $systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.composerMode',
                'value' => $GLOBALS['LANG']->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'),
                'iconIdentifier' => 'sysinfo-composer-mode',
            ];
        }

        return $systemInformation;
    }

    /**
     * Gets the current GIT revision and branch
     */
    protected function getGitRevision(array $systemInformation): array
    {
        if (!StringUtility::endsWith(TYPO3_version, '-dev') || SystemEnvironmentBuilder::isFunctionDisabled('exec')) {
            return $systemInformation;
        }
        // check if git exists
        CommandUtility::exec('git --version', $_, $returnCode);
        if ((int)$returnCode !== 0) {
            // git is not available
            return $systemInformation;
        }

        $revision = trim(CommandUtility::exec('git rev-parse --short HEAD'));
        $branch = trim(CommandUtility::exec('git rev-parse --abbrev-ref HEAD'));
        if (!empty($revision) && !empty($branch)) {
            $systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.gitrevision',
                'value' => sprintf('%s [%s]', $revision, $branch),
                'iconIdentifier' => 'sysinfo-git'
            ];
        }

        return $systemInformation;
    }

    /**
     * Gets the system kernel and version
     */
    protected function getOperatingSystem(): array
    {
        $kernelName = php_uname('s');
        switch (strtolower($kernelName)) {
            case 'linux':
                $icon = 'linux';
                break;
            case 'darwin':
                $icon = 'apple';
                break;
            default:
                $icon = 'windows';
        }
        return [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.operatingsystem',
            'value' => $kernelName . ' ' . php_uname('r'),
            'iconIdentifier' => 'sysinfo-os-' . $icon
        ];
    }
}
