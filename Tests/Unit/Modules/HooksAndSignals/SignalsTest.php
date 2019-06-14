<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\HooksAndSignals;

use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals;
use TYPO3\CMS\Adminpanel\Log\InMemoryLogWriter;
use TYPO3\CMS\Beuser\Domain\Model\ModuleData;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SignalsTest extends UnitTestCase
{

    protected $signals;

    protected $languageService;

    protected $serverRequest;

    private const REQUEST_ID = '12345RequestId';

    protected function setUp()
    {
        parent::setUp();
        $this->signals = new Signals();
        $this->languageService = $this->prophesize(LanguageService::class);
        $serverRequestProphecy = $this->prophesize(ServerRequestInterface::class);
        $serverRequestProphecy->getAttribute('adminPanelRequestId')->willReturn(self::REQUEST_ID);
        $this->serverRequest = $serverRequestProphecy->reveal();
    }

    /**
     * @test
     */
    public function getIdentifierReturnsIdentifier(): void
    {
        $identifier = $this->signals->getIdentifier();
        self::assertSame('psychomieze_debug_signals', $identifier);
    }

    /**
     * @test
     */
    public function getLabelGetsTranslatedLabel(): void
    {
        $label = 'My Label';
        $this->languageService->sL(Argument::any())->willReturn($label);
        $GLOBALS['LANG'] = $this->languageService->reveal();

        $result = $this->signals->getLabel();
        self::assertSame($label, $result);
    }

    /**
     * @test
     */
    public function getDataToStoreStoresRelevantEntriesFromInMemoryLogWithUniqueId(): void
    {
        $matchingLogRecord = new LogRecord(
            'TYPO3.CMS.Extbase.SignalSlot.Dispatcher',
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
        $result = $this->signals->getDataToStore($this->serverRequest);
        self::assertSame(self::REQUEST_ID, $result['requestId']);
        self::assertStringStartsWith('signal-', key($result['signals']));
        self::assertSame(20, \strlen(key($result['signals'])));
        self::assertContains($matchingLogRecord, $result['signals']);
    }

}
