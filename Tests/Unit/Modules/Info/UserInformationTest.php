<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Info;

use Psychomieze\AdminpanelExtended\Modules\Info\UserInformation;
use TYPO3\CMS\Adminpanel\ModuleApi\AbstractSubModule;
use TYPO3\CMS\Core\Localization\LanguageService;
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
}
