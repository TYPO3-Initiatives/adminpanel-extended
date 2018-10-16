<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModuleDataService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param string $moduleClass Class name of module to get data for
     * @param string $requestId
     * @return null|\TYPO3\CMS\Adminpanel\ModuleApi\ModuleData
     */
    private $cacheManager;

    private const NO_SUCH_CACHE = 'Configuration error: The adminpanel is activated but the adminpanel_requestcache was not found.';

    public function __construct(CacheManager $cacheManager = null)
    {
        $this->cacheManager = $cacheManager ?? GeneralUtility::makeInstance(CacheManager::class);
    }

    public function getModuleDataByRequestId(string $moduleClass, string $requestId): ?ModuleData
    {
        $moduleData = null;
        try {
            $data = $this->getDataFromCache($requestId);
            foreach ($data ?? [] as $module) {
                if ($module instanceof $moduleClass) {
                    $moduleData = $data->offsetGet($module);
                    break;
                }
            }
        } catch (NoSuchCacheException $e) {
            $this->logger->warning(
                self::NO_SUCH_CACHE
            );
        }
        return $moduleData instanceof ModuleData ? $moduleData : null;
    }


    /**
     * @param string $requestId
     * @return \SplObjectStorage
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    private function getDataFromCache(string $requestId): \SplObjectStorage
    {
        $cache = $this->cacheManager->getCache('adminpanel_requestcache');
        return $cache->get($requestId);
    }
}
