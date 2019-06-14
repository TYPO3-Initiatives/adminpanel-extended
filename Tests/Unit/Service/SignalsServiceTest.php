<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Service;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psychomieze\AdminpanelExtended\Service\SignalsService;
use Psychomieze\AdminpanelExtended\Tests\Unit\Fixtures\FirstDataProviderModuleFixture;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SignalsServiceTest extends UnitTestCase
{

    /**
     * @test
     */
    public function getSignalDataFromLogRecordReturnsSignalArguments(): void
    {
        $signalArguments = [
            'foo',
            'bar'
        ];
        $record = new LogRecord('TYPO3.CMS.Extbase.SignalSlot.Dispatcher', LogLevel::DEBUG, '', [
            'signalArguments' => $signalArguments,
            'foo' => 'bar'
        ]);

        $signalsService = new SignalsService();
        $result = $signalsService->getSignalDataFromLogRecord($record);

        self::assertSame($signalArguments, $result);
    }

    /**
     * @test
     */
    public function getSignalDataFromLogRecordReturnsClassNameIfLogRecordContainsObjects(): void
    {
        $signalArguments = ['foo', 'baa' => new FirstDataProviderModuleFixture()];
        $record = new LogRecord(
            'TYPO3.CMS.Extbase.SignalSlot.Dispatcher',
            LogLevel::DEBUG,
            '',
            [
                'signalArguments' => $signalArguments,
                'foo' => 'bar'
            ]
        );

        $signalsService = new SignalsService();
        $result = $signalsService->getSignalDataFromLogRecord($record);

        $expected = ['foo', 'baa' => FirstDataProviderModuleFixture::class];
        self::assertSame($expected, $result);
    }
}
