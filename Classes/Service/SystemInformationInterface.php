<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Service;

/**
 * Interface SystemInformationInterface
 */
interface SystemInformationInterface
{
    /**
     * Collect the system information
     */
    public function collectInformation(): array;
}
