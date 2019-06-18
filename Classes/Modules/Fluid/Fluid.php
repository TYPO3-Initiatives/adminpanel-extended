<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\Fluid;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\InitializableInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class Fluid extends AbstractSubModule implements ContentProviderInterface, DataProviderInterface, InitializableInterface
{
    /**
     * @var bool
     */
    private $showFluidDebug = false;

    /**
     * Initialize the module - runs early in a TYPO3 request
     *
     * @param ServerRequestInterface $request
     */
    public function initializeModule(ServerRequestInterface $request): void
    {
        $this->showFluidDebug = (bool)$this->getBackendUser()->uc['AdminPanel']['preview_showFluidDebug'];
        if (!$this->showFluidDebug) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['f'][] = 'Psychomieze\AdminpanelExtended\ViewHelpers\Rendering';
        }
    }

    /**
     * Main method for content generation of an admin panel module.
     * Return content as HTML. For modules implementing the DataProviderInterface
     * the "ModuleData" object is automatically filled with the stored data - if
     * no data is given a "fresh" ModuleData object is injected.
     *
     * @param \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData $data
     * @return string
     */
    public function getContent(ModuleData $data): string
    {
        return 'content';
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    public function getDataToStore(ServerRequestInterface $request): ModuleData
    {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS);
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($request);
//        die;

        return new ModuleData();
    }

    /**
     * Identifier for this module,
     * for example "preview" or "cache"
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'psychomieze_fluid_fluid';
    }

    /**
     * Module label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_fluid.xlf:submodule.fluid.label'
        );
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
