<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\HooksAndSignals;

use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\DummyFirstLevelArrayObject;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Hooks;
use TYPO3\CMS\Adminpanel\Log\InMemoryLogWriter;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class HooksTest extends UnitTestCase
{
    protected $hooks;

    protected $languageService;

    protected $serverRequest;

    protected function setUp()
    {
        parent::setUp();
        $this->hooks = new Hooks();
        $this->languageService = $this->prophesize(LanguageService::class);
        $this->serverRequest = $this->prophesize(ServerRequestInterface::class)->reveal();
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier(): void
    {
        $identifier = $this->hooks->getIdentifier();
        self::assertSame('psychomieze_debug_hooks', $identifier);
    }

    /**
     * @test
     */
    public function getLabelGetsTranslatedLabel(): void
    {
        $label = 'My Label';
        $this->languageService->sL(Argument::any())->willReturn($label);
        $GLOBALS['LANG'] = $this->languageService->reveal();

        $result = $this->hooks->getLabel();
        self::assertSame($label, $result);
    }

    /**
     * @test
     */
    public function getDataToStoreStoresDataFromInMemoryLog(): void
    {
        $matchingLogRecord = new LogRecord(
            'Psychomieze.AdminpanelExtended.Modules.HooksAndSignals.LoggedArray',
            LogLevel::DEBUG,
            '',
            [
                'foo' => 'bar',
            ]
        );
        $nonMatchingLogRecord = new LogRecord(
            'Some.Other.Component',
            LogLevel::DEBUG,
            '',
            [
                'foo' => 'bar',
            ]
        );
        $log = [
            $matchingLogRecord,
            $nonMatchingLogRecord
        ];
        InMemoryLogWriter::$log = $log;
        $result = $this->hooks->getDataToStore($this->serverRequest);
        $expected = new ModuleData([
            'hooks' => [$matchingLogRecord]
        ]);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function initializeModuleSetsScOptionsToDummyObject(): void
    {
        $scOptions = ['something' => 'rotten'];
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'] = $scOptions;
        $this->hooks->initializeModule($this->serverRequest);
        /** @var \ArrayObject $result */
        $result = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'];
        self::assertInstanceOf(DummyFirstLevelArrayObject::class, $result);
        self::assertSame($scOptions, $result->getArrayCopy());
    }

}
