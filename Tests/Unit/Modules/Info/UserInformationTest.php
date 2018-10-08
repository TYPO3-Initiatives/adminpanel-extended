<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Info;

use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psychomieze\AdminpanelExtended\Domain\Repository\FrontendUserSessionRepository;
use Psychomieze\AdminpanelExtended\Modules\Info\UserInformation;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Adminpanel\ModuleApi\ContentProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\DataProviderInterface;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ResourceProviderInterface;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserSessionRepository;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class UserInformationTest
 */
class UserInformationTest extends UnitTestCase
{
    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Info\UserInformation|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;

        $this->subject = $this->createPartialMock(UserInformation::class, ['isPageLocked']);
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
        $viewProphecy = $this->prophesize(StandaloneView::class);
        $viewProphecy->setTemplatePathAndFilename(Argument::any())->shouldBeCalled();
        $viewProphecy->assignMultiple($this->getModuleDataFixture()->getArrayCopy())->shouldBeCalled();
        $viewProphecy->render()->willReturn('some html');
        GeneralUtility::addInstance(StandaloneView::class, $viewProphecy->reveal());

        $this->subject->getContent($this->getModuleDataFixture());

        static::assertInstanceOf(ContentProviderInterface::class, $this->subject);
    }

    /**
     * @test
     */
    public function getDataToStoreShouldFetchModuleSpecificData(): void
    {
        $requestProphecy = $this->prophesize(ServerRequestInterface::class);

        $this->subject
            ->expects(static::once())
            ->method('isPageLocked')
            ->willReturn(true);

        $objectManagerProphecy = $this->prophesize(ObjectManager::class);
        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManagerProphecy->reveal());

        $frontendUserSessionRepositoryProphecy = $this->prophesize(FrontendUserSessionRepository::class);
        $frontendUserSessionRepositoryProphecy->findAllActive()->willReturn([1, 2, 3, 4]);
        $objectManagerProphecy->get(FrontendUserSessionRepository::class)
            ->willReturn($frontendUserSessionRepositoryProphecy->reveal());

        $backendUserSessionRepositoryProphecy = $this->prophesize(BackendUserSessionRepository::class);
        $backendUserSessionRepositoryProphecy->findAllActive()->willReturn([5, 6, 7, 8, 9]);
        $objectManagerProphecy->get(BackendUserSessionRepository::class)
            ->willReturn($backendUserSessionRepositoryProphecy->reveal());

        $actual = $this->subject->getDataToStore($requestProphecy->reveal());

        static::assertEquals(
            $this->getModuleDataFixture(true, 4, 5),
            $actual
        );
        static::assertInstanceOf(DataProviderInterface::class, $this->subject);
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
            'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'],
            'onlineFrontendUsers' => $onlineFrontendUsers,
            'onlineBackendUsers' => $onlineBackendUsers,
        ]);
    }
}
