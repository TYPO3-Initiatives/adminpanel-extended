<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Controller;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Prophecy\Argument;
use Psychomieze\AdminpanelExtended\Controller\TemplatesAjaxController;
use Psychomieze\AdminpanelExtended\Modules\Fluid\Templates;
use Psychomieze\AdminpanelExtended\Service\ModuleDataService;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TemplatesAjaxControllerTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;
    /**
     * @var \Psychomieze\AdminpanelExtended\Controller\TemplatesAjaxController
     */
    protected $subject;
    /**
     * @var \Psr\Http\Message\ServerRequestInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    protected $request;

    protected function setUp(): void
    {
        $this->subject = new TemplatesAjaxController();
        $this->request = new ServerRequest();
        $this->request = $this->request->withQueryParams(['templateId' => 'some id', 'requestId' => 'some id']);
    }

    /**
     * @test
     * @param array $queryParams
     * @dataProvider queryParamsDataProvider
     */
    public function getDataThrowsExceptionValidatesRequiredParameters(array $queryParams): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing parameters, templateId and requestId need to be set.');
        $this->expectExceptionCode(1561386190);

        $this->request = $this->request->withQueryParams($queryParams);
        $actual = $this->subject->getData($this->request);

        self::assertSame($queryParams, $this->request->getQueryParams());
    }

    /**
     * @test
     * @dataProvider moduleDataProvider
     * @param $moduleData
     */
    public function getDataReturnsEmptyJsonResponseIfModuleDataIsNotFoundOrTemplateRecordIsNotAnArray($moduleData): void
    {
        $moduleDataService = $this->prophesize(ModuleDataService::class);
        $moduleDataService->getModuleDataByRequestId(Templates::class, Argument::any())
            ->willReturn($moduleData);
        GeneralUtility::addInstance(ModuleDataService::class, $moduleDataService->reveal());

        $response = $this->subject->getData($this->request);

        static::assertSame(404, $response->getStatusCode());
        static::assertSame('', $response->getBody()->getContents());
    }

    /**
     * @return array
     */
    public function queryParamsDataProvider(): array
    {
        return [
            'empty query params' => [
                'queryParams' => [],
            ],
            'only template id' => [
                'queryParams' => ['templateId' => 'some id'],
            ],
            'only request id' => [
                'queryParams' => ['requestId' => 'some id'],
            ],
            'empty template id' => [
                'queryParams' => ['templateId' => '', 'requestId' => 'some id'],
            ],
            'empty request id' => [
                'queryParams' => ['templateId' => 'some id', 'requestId' => ''],
            ],
            'empty request id and template id' => [
                'queryParams' => ['templateId' => '', 'requestId' => ''],
            ],
        ];
    }

    public function moduleDataProvider(): array
    {
        return [
            ['moduleData' => null],
            ['moudleData' => new ModuleData()],
        ];
    }
}
