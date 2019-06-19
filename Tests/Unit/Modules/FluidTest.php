<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Prophecy\Argument;
use Psychomieze\AdminpanelExtended\Modules\Fluid;
use TYPO3\CMS\Adminpanel\Service\ConfigurationService;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class FluidTest extends UnitTestCase
{
    /**
     * @var bool
     */
    protected $resetSingletonInstances = true;

    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Fluid
     */
    protected $subject;

    /**
     * @var \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected $languageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->languageService = $this->prophesize(LanguageService::class);
        $configurationService = $this->prophesize(ConfigurationService::class);
        $configurationService->getMainConfiguration()->willReturn([]);
        GeneralUtility::setSingletonInstance(ConfigurationService::class, $configurationService->reveal());

        $this->subject = new Fluid();
    }

    /**
     * @test
     */
    public function getIconIdentifierReturnsIconIdentifier(): void
    {
        static::assertSame('actions-document', $this->subject->getIconIdentifier());
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier(): void
    {
        static::assertSame('psychomieze_fluid', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getLabel(): void
    {
        $label = 'My Label';
        $this->languageService->sL(Argument::any())->willReturn($label);
        $GLOBALS['LANG'] = $this->languageService->reveal();

        static::assertSame($label, $this->subject->getLabel());
    }

    /**
     * @test
     */
    public function getShortInfo(): void
    {
        static::assertSame('', $this->subject->getShortInfo());
    }
}
