<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Composer;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Class ScriptHelper
 *
 * @codeCoverageIgnore Build Helper
 */
final class ScriptHelper
{
    private const EXTENSION_DIRECTORY = __DIR__ . '/../../.Build/web/typo3conf/ext';

    private const LINK = self::EXTENSION_DIRECTORY . '/adminpanel_extended';

    public static function ensureExtensionStructure(): void
    {
        if (!is_dir(self::EXTENSION_DIRECTORY) &&
            !mkdir($concurrentDirectory = self::EXTENSION_DIRECTORY, 0775, true) &&
            !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" could not be created', self::EXTENSION_DIRECTORY));
        }

        if (!is_link(self::LINK)) {
            symlink(dirname(__DIR__, 2) . '/', self::LINK);
        }
    }
}
