<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\Signals;
use TYPO3\CMS\Adminpanel\ModuleApi\ModuleData;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModuleDataService implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getModuleDataByRequestId(string $requestId): ?ModuleData
    {
        $moduleData = null;
        try {
            $data = $this->getDataFromCache($requestId);
            foreach ($data ?? [] as $module) {
                if ($module instanceof Signals) {
                    $moduleData = $data->offsetGet($module);
                    break;
                }
            }
        } catch (NoSuchCacheException $e) {
            $this->logger->warning(
                'Configuration error: The adminpanel is activated but the adminpanel_requestcache was not found.'
            );
        }
        return $moduleData instanceof ModuleData ? $moduleData : null;
    }


    /**
     * @param string $requestId
     * @return \ArrayObject
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    private function getDataFromCache(string $requestId): \ArrayObject
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cache = $cacheManager->getCache('adminpanel_requestcache');
        return $cache->get($requestId);
    }
}
