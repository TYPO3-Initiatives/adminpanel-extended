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
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TemplatesTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;

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
                'EXT:adminpanel_extended/Resources/Public/Css/vendor/desert.css',
            ],
            $this->subject->getCssFiles()
        );
    }

    /**
     * @test
     */
    public function getContent(): void
    {
        $moduleData = new ModuleData();
        $moduleData['requestId'] = 'some request id';

        $view = $this->prophesize(StandaloneView::class);
        $view->setTemplatePathAndFilename('EXT:adminpanel_extended/Resources/Private/Templates/Fluid/Templates.html')->shouldBeCalled();
        $view->assignMultiple($moduleData->getArrayCopy())->shouldBeCalled();
        $view->assign('templateDataUri', 'some url')->shouldBeCalled();
        $view->render()->willReturn('some html');
        GeneralUtility::addInstance(StandaloneView::class, $view->reveal());

        $uriBuilder = $this->prophesize(UriBuilder::class);
        GeneralUtility::setSingletonInstance(UriBuilder::class, $uriBuilder->reveal());

        $uriBuilder->buildUriFromRoute(
            'ajax_adminPanelExtended_templateData',
            ['requestId' => $moduleData['requestId']]
        )->willReturn('some url');

        $this->subject->getContent($moduleData);
    }

    /**
     * @test
     */
    public function getJavaScriptFiles(): void
    {
        static::assertSame(
            [
                'EXT:adminpanel_extended/Resources/Public/JavaScript/Templates.js',
                'EXT:adminpanel_extended/Resources/Public/JavaScript/vendor/prettify.js',
            ],
            $this->subject->getJavaScriptFiles()
        );
    }
}
