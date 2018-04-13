<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\ViewHelpers;

use TYPO3\CMS\Install\ViewHelpers\PhpInfoViewHelper as InstallToolPhpInfoViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Utility class for phpinfo()
 * @internal
 */
class PhpInfoViewHelper extends InstallToolPhpInfoViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('tableClass', 'string', 'Class attribute which will be added to all tables.', false, '');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $html = parent::renderStatic(
            $arguments,
            $renderChildrenClosure,
            $renderingContext
        );

        if (isset($arguments['tableClass'])) {
            //Add class attribute to every <table> element
            $replacement = '$1 class="' . $arguments['tableClass'] . '">';
            $html = \preg_replace('/(<table\b[^><]*)>/i', $replacement, $html);
        }

        return $html;
    }
}
