<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Prophecy\Argument;
use Psychomieze\AdminpanelExtended\Modules\Fluid\General;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class GeneralTest extends UnitTestCase
{
    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Fluid\General
     */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new General();
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier(): void
    {
        static::assertSame('psychomieze_fluid_general', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getLabelReturnsLocalizedLabel(): void
    {
        $label = 'My label';
        $languageService = $this->prophesize(LanguageService::class);
        $languageService->sL(Argument::any())->willReturn($label);
        $GLOBALS['LANG'] = $languageService->reveal();

        $actual = $this->subject->getLabel();
        static::assertSame($label, $actual);
    }

    /**
     * @test
     */
    public function getDataToStoreShouldReturnModuleDataToStore(): void
    {
        $request = $this->prophesize(ServerRequest::class);
        $actual = $this->subject->getDataToStore($request->reveal());

        static::assertArrayHasKey('fluidNamespaces', $actual);
        static::assertSame($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces'], $actual['fluidNamespaces']);

        static::assertArrayHasKey('preProcessors', $actual);
        static::assertSame($GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['preProcessors'], $actual['preProcessors']);
    }

    /**
     * @test
     */
    public function getContentShouldReturnHtmlFromStandaloneView(): void
    {
        $data = new ModuleData();

        $view = $this->prophesize(StandaloneView::class);
        $view->setTemplatePathAndFilename('EXT:adminpanel_extended/Resources/Private/Templates/Fluid/General.html')->shouldBeCalled();
        $view->assignMultiple($data->getArrayCopy())->shouldBeCalled();
        $view->render()->willReturn('html code');
        GeneralUtility::addInstance(StandaloneView::class, $view->reveal());

        $actual = $this->subject->getContent($data);
    }
}
