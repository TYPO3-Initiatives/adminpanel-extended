<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules;

use Psychomieze\AdminpanelExtended\Service\SystemInformationInterface;
use Psychomieze\AdminpanelExtended\Service\SystemInformationService;
use TYPO3\CMS\Adminpanel\Modules\InfoModule as AdminpanelInfoModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Extends original InfoModule with more functionality
 */
class InfoModule extends AdminpanelInfoModule
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
                'systemEnvironmentInformation' => $this->getSystemInformationService()->collectInformation()
            ]
        ]);

        return $view->render();
    }

    /**
     * @return \Psychomieze\AdminpanelExtended\Service\SystemInformationInterface
     */
    protected function getSystemInformationService(): SystemInformationInterface
    {
        return GeneralUtility::makeInstance(SystemInformationService::class);
    }
}
