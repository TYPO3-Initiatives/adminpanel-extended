<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Debug;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psychomieze\AdminpanelExtended\Modules\Debug\InternalContentObjects;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class InternalContentObjectsTest
 */
class InternalContentObjectsTest extends UnitTestCase
{
    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Debug\InternalContentObjects
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new InternalContentObjects();
    }

    /**
     * @test
     */
    public function getIdentifierReturnsUniqueSubModuleIdentifier(): void
    {
        static::assertSame('internal-content-objects', $this->subject->getIdentifier());
    }

    /**
     * @test
     */
    public function getLabelShouldCallLanguageServiceForLocalizedLanguageLabel(): void
    {
        $languageServiceProphecy = $this->prophesize(LanguageService::class);
        $labelIdentifier = 'LLL:EXT:adminpanel_extended/Resources/Private/Language/locallang_debug.xlf:submodule.internalContentObjects.label';
        $languageServiceProphecy->sL($labelIdentifier)->willReturn('some LLL');
        $GLOBALS['LANG'] = $languageServiceProphecy->reveal();

        $this->subject->getLabel();

        $languageServiceProphecy->sL($labelIdentifier)->shouldHaveBeenCalledTimes(1);
    }
}
