<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Service;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Log\LoggerInterface;
use Psychomieze\AdminpanelExtended\Service\ModuleDataService;
use Psychomieze\AdminpanelExtended\Tests\Unit\Fixtures\FirstDataProviderModuleFixture;
use Psychomieze\AdminpanelExtended\Tests\Unit\Fixtures\SecondDataProviderModuleFixture;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleDataStorageCollection;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ModuleDataServiceTest extends UnitTestCase
{

    protected $requestId;

    protected function setUp()
    {
        parent::setUp();
        $this->requestId = '1234requestId';
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function getModuleDataByRequestIdReturnsModuleDataInstance(): void
    {
        $requestId = $this->requestId;

        $moduleData = new ModuleData(['Dummy']);
        $module = new FirstDataProviderModuleFixture();
        $module2 = new SecondDataProviderModuleFixture();

        $cacheData = new ModuleDataStorageCollection();
        $cacheData->addModuleData($module, new ModuleData());
        $cacheData->addModuleData($module2, $moduleData);

        $cacheFrontend = $this->prophesize(FrontendInterface::class);
        $cacheFrontend->get($requestId)->willReturn($cacheData);
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getCache('adminpanel_requestcache')->willReturn($cacheFrontend->reveal());

        $moduleDataService = new ModuleDataService($cacheManager->reveal());
        $result = $moduleDataService->getModuleDataByRequestId(SecondDataProviderModuleFixture::class, $requestId);

        self::assertSame($moduleData, $result);
    }

    /**
     * @test
     */
    public function getModuleDataByRequestIdLogsCacheException(): void
    {
        $cacheManager = $this->prophesize(CacheManager::class);
        $cacheManager->getCache('adminpanel_requestcache')->willThrow(new NoSuchCacheException());
        $logger = $this->prophesize(LoggerInterface::class);

        $moduleDataService = new ModuleDataService($cacheManager->reveal());
        $moduleDataService->setLogger($logger->reveal());
        $moduleDataService->getModuleDataByRequestId(FirstDataProviderModuleFixture::class, $this->requestId);

        $logger->warning('Configuration error: The adminpanel is activated but the adminpanel_requestcache was not found.')->shouldHaveBeenCalled();
    }
}
