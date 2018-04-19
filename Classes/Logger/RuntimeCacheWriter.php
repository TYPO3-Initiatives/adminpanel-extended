<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Logger;


use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RuntimeCacheWriter implements WriterInterface
{

    /**
     * Writes the log record
     *
     * @param \TYPO3\CMS\Core\Log\LogRecord $record Log record
     * @return \TYPO3\CMS\Core\Log\Writer\WriterInterface $this
     * @throws \Exception
     */
    public function writeLog(\TYPO3\CMS\Core\Log\LogRecord $record)
    {
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $runtimeCache = $cacheManager->getCache('cache_runtime');
        $component = str_replace('.', '_', $record->getComponent());
        $runtimeCache->set(sha1(json_encode($record->getData())), $record, [$component]);
        return $this;
    }
}