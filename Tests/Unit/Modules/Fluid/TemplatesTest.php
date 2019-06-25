<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psychomieze\AdminpanelExtended\Modules\Fluid\Templates;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TemplatesTest extends UnitTestCase
{

    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Fluid\Templates
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Templates();
    }

    /**
     * @test
     */
    public function getIdentifier(): void
    {
        static::assertSame('psychomieze_fluid_templates', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getLabel(): void
    {
        $languageService = $this->prophesize(LanguageService::class);
        $languageService->sL('LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_fluid.xlf:submodule.templates.label')
            ->willReturn('some label');
        $GLOBALS['LANG'] = $languageService->reveal();

        self::assertSame('some label', $this->subject->getLabel());

        unset($GLOBALS['LANG']);
    }

    /**
     * @test
     */
    public function getCssFiles(): void
    {
        static::assertSame(
            [
                'EXT:adminpanel_extended/Resources/Public/Css/Templates.css',
                'EXT:adminpanel_extended/Resources/Public/Css/vendor/desert.css'
            ],
            $this->subject->getCssFiles()
        );
    }

    /**
     * @test
     */
    public function getContent(): void
    {
        $actual = $this->subject->getContent();
    }

    /**
     * @test
     */
    public function getJavaScriptFiles(): void
    {
        static::assertSame(
            [
                'EXT:adminpanel_extended/Resources/Public/JavaScript/Templates.js',
                'EXT:adminpanel_extended/Resources/Public/JavaScript/vendor/prettify.js'
            ],
            $this->subject->getJavaScriptFiles()
        );
    }

    /**
     * @test
     */
    public function getDataToStore(): void
    {
        $actual = $this->subject->getDataToStore();
    }
}
