<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Info;

use Prophecy\Argument;
use Psychomieze\AdminpanelExtended\Modules\Info\UserInformation;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ResourceProviderInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class UserInformationTest
 */
class UserInformationTest extends UnitTestCase
{
    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Info\UserInformation
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new UserInformation();
    }

    /**
     * @test
     */
    public function getIdentifierReturnsStaticModuleIdentifier(): void
    {
        static::assertSame('info_userinformation', $this->subject->getIdentifier());

        static::assertInstanceOf(AbstractSubModule::class, $this->subject);
    }

    /**
     * @test
     */
    public function getLabelShouldCallLanguageServiceForLocalizedLanguageLabel(): void
    {
        $languageServiceProphecy = $this->prophesize(LanguageService::class);
        $labelIdentifier = 'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_info.xlf:submodule.userinformation.label';
        $languageServiceProphecy->sL($labelIdentifier)->willReturn('some LLL');
        $GLOBALS['LANG'] = $languageServiceProphecy->reveal();

        $this->subject->getLabel();

        $languageServiceProphecy->sL($labelIdentifier)->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @test
     */
    public function resourceProviderMethodsShouldProvideAdditonalCssResources(): void
    {
        $expectedJSResources = [];
        $expectedCSSResources = ['EXT:adminpanel_extended/Resources/Public/Css/Messages.css'];

        static::assertInstanceOf(ResourceProviderInterface::class, $this->subject);
        static::assertSame($expectedJSResources, $this->subject->getJavaScriptFiles());
        static::assertSame($expectedCSSResources, $this->subject->getCssFiles());
    }

    /**
     * @test
     */
    public function getContentShouldUsePassedModuleDataAndForwardTheseDataToTheView(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] = 'ddmmyy';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'] = 'hhmm';

        $viewProphecy = $this->prophesize(StandaloneView::class);
        $viewProphecy->setTemplatePathAndFilename(Argument::any())->shouldBeCalled();
        $viewProphecy->assignMultiple($this->getModuleDataFixture()->getArrayCopy())->shouldBeCalled();
        $viewProphecy->render()->willReturn('some html');
        GeneralUtility::addInstance(StandaloneView::class, $viewProphecy->reveal());

        $this->subject->getContent($this->getModuleDataFixture());

        static::assertInstanceOf(ContentProviderInterface::class, $this->subject);
    }

    /**
     * @param bool $isPageBeingEdited
     * @param int $onlineFrontendUsers
     * @param int $onlineBackendUsers
     * @return \TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    protected function getModuleDataFixture(
        bool $isPageBeingEdited = false,
        int $onlineFrontendUsers = 0,
        int $onlineBackendUsers = 0
    ): ModuleData {
        return new ModuleData([
            'isPageBeingEdited' => $isPageBeingEdited,
            'dataFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'onlineFrontendUsers' => $onlineFrontendUsers,
            'onlineBackendUsers' => $onlineBackendUsers,
        ]);
    }
}
