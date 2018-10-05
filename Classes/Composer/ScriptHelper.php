<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Composer;

final class ScriptHelper
{
    private const EXTENSION_DIRECTORY = './.Build/web/typo3conf/ext';

    private const LINK = self::EXTENSION_DIRECTORY . '/adminpanel_extended';

    public static function ensureExtensionStructure(): void
    {
        if (!is_dir(self::EXTENSION_DIRECTORY) && !mkdir(self::EXTENSION_DIRECTORY)) {
            throw new \RuntimeException(sprintf('Directory "%s" could not be created', self::EXTENSION_DIRECTORY));
        }

        if (!is_link(self::LINK)) {
            symlink('../../../../.', self::LINK);
        }
    }
}
