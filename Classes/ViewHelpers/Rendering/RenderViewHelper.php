<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\ViewHelpers\Rendering;

class RenderViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\RenderViewHelper
{
    public function render()
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->arguments);
        die;
    }
}
