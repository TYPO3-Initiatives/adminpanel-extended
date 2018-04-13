<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;

use TYPO3\CMS\Backend\Toolbar\Enumeration\InformationStatus;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class SystemInformationService
 * @internal
 */
final class SystemInformationService implements SingletonInterface, SystemInformationInterface
{
    /**
     * @var array
     */
    private static $systemInformation = [];

    /**
     * Collect the system information
     */
    public function collectInformation(): array
    {
        if (empty(self::$systemInformation)) {
            $this->getTypo3Version();
            $this->getWebServer();
            $this->getPhpVersion();
            $this->getDatabase();
            $this->getApplicationContext();
            $this->getComposerMode();
            $this->getGitRevision();
            $this->getOperatingSystem();
        }

        return self::$systemInformation;
    }

    /**
     * Gets the TYPO3 version
     */
    private function getTypo3Version(): void
    {
        self::$systemInformation[] = [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.typo3-version',
            'value' => VersionNumberUtility::getCurrentTypo3Version(),
            'iconIdentifier' => 'sysinfo-typo3-version'
        ];
    }

    /**
     * Gets the webserver software
     */
    private function getWebServer(): void
    {
        self::$systemInformation[] = [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.webserver',
            'value' => $_SERVER['SERVER_SOFTWARE'],
            'iconIdentifier' => 'sysinfo-webserver'
        ];
    }

    /**
     * Gets the PHP version
     */
    private function getPhpVersion(): void
    {
        self::$systemInformation[] = [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.phpversion',
            'value' => PHP_VERSION,
            'iconIdentifier' => 'sysinfo-php-version'
        ];
    }

    /**
     * Get the database info
     */
    private function getDatabase(): void
    {
        foreach (GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionNames() as $connectionName) {
            self::$systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.database',
                'titleAddition' => $connectionName,
                'value' => GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getConnectionByName($connectionName)
                    ->getServerVersion(),
                'iconIdentifier' => 'sysinfo-database'
            ];
        }
    }

    /**
     * Gets the application context
     */
    private function getApplicationContext(): void
    {
        $applicationContext = GeneralUtility::getApplicationContext();

        self::$systemInformation[] = [
            'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.applicationcontext',
            'value' => (string)$applicationContext,
            'status' => $applicationContext->isProduction() ? InformationStatus::STATUS_OK : InformationStatus::STATUS_WARNING,
            'iconIdentifier' => 'sysinfo-application-context'
        ];
    }

    /**
     * Adds the information if the Composer mode is enabled or disabled to the displayed system information
     */
    private function getComposerMode(): void
    {
        if (Environment::isComposerMode()) {
            self::$systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.composerMode',
                'value' => $GLOBALS['LANG']->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled'),
                'iconIdentifier' => 'sysinfo-composer-mode',
            ];
        }
    }

    /**
     * Gets the current GIT revision and branch
     */
    private function getGitRevision(): void
    {
        if (!StringUtility::endsWith(TYPO3_version, '-dev') || SystemEnvironmentBuilder::isFunctionDisabled('exec')) {
            return;
        }
        // check if git exists
        CommandUtility::exec('git --version', $_, $returnCode);
        if ((int)$returnCode !== 0) {
            // git is not available
            return;
        }

        $revision = trim(CommandUtility::exec('git rev-parse --short HEAD'));
        $branch = trim(CommandUtility::exec('git rev-parse --abbrev-ref HEAD'));
        if (!empty($revision) && !empty($branch)) {
            self::$systemInformation[] = [
                'title' => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:toolbarItems.sysinfo.gitrevision',
                'value' => sprintf('%s [%s]', $revision, $branch),
                'iconIdentifier' => 'sysinfo-git'
            ];
        }
    }

    /**
     * Gets the system kernel and version
     */
    private function getOperatingSystem(): array
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
