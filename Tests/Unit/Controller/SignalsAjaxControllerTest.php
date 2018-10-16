<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Controller;

use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;
use Psychomieze\AdminpanelExtended\Controller\SignalsAjaxController;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals;
use Psychomieze\AdminpanelExtended\Service\ModuleDataService;
use Psychomieze\AdminpanelExtended\Service\SignalsService;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class SignalsAjaxControllerTest extends UnitTestCase
{
    protected $signalsService;

    protected $moduleDataService;

    protected $signalsAjaxController;

    private const SIGNAL_ID = '123456Id';
    private const REQUEST_ID = 'requestId';

    protected function setUp()
    {
        parent::setUp();
        $this->signalsService = $this->prophesize(SignalsService::class);
        $this->moduleDataService = $this->prophesize(ModuleDataService::class);
        $this->signalsAjaxController = new SignalsAjaxController(
            $this->signalsService->reveal(),
            $this->moduleDataService->reveal()
        );
    }

    /**
     * @test
     */
    public function getDataThrowsExceptionOnMissingArguments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1539121176);

        $this->signalsAjaxController->getData($this->prophesize(ServerRequestInterface::class)->reveal());
    }

    /**
     * @test
     */
    public function getDataReturns404ResponseIfNoDataIsFound(): void
    {
        $serverRequest = $this->setUpServerRequestProphecy();
        $response = $this->signalsAjaxController->getData($serverRequest->reveal());
        self::assertSame(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function getDataReturnsSignalData(): void
    {
        $serverRequest = $this->setUpServerRequestProphecy();
        $signalArguments = ['that' => 'was', 'just' => 'a', 'dream'];
        $logRecord = new LogRecord(
            'TYPO3.CMS.Extbase.SignalSlot.Dispatcher', LogLevel::DEBUG, '', [
                'signalArguments' => $signalArguments,
                'foo' => 'bar',
            ]
        );
        $moduleData = new ModuleData(
            [
                'signals' => [
                    self::SIGNAL_ID => $logRecord,
                ],
            ]
        );

        $this->moduleDataService->getModuleDataByRequestId(Signals::class, self::REQUEST_ID)->willReturn(
            $moduleData
        );
        $this->signalsService->getSignalDataFromLogRecord($logRecord)->willReturn($signalArguments);

        $expectedData = [
            'data' => $signalArguments,
            'signalId' => self::SIGNAL_ID,
        ];

        $result = $this->signalsAjaxController->getData($serverRequest->reveal());
        self::assertSame(200, $result->getStatusCode());
        self::assertSame($expectedData, \json_decode($result->getBody()->getContents(), true));
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy|ServerRequestInterface
     */
    private function setUpServerRequestProphecy(): ObjectProphecy
    {
        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $serverRequest->getQueryParams()->willReturn(
            [
                'signalId' => self::SIGNAL_ID,
                'requestId' => self::REQUEST_ID,
            ]
        );
        return $serverRequest;
    }
}
