<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\HooksAndSignals;

use ArrayObject;

class LoggedArray extends ArrayObject
{
    protected $parent = '';

    public static $things = [];

    public function setParent(string $parent)
    {
        $this->parent = $parent;
    }

    public function offsetExists($index)
    {
        if (parent::offsetExists($index)) {
            $hook = $this->parent . '[\'' . $index . '\']';
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            array_shift($backtrace);
            $this->things[] = [
                'hook' => $hook,

            ];

            return true;
        }
        return false;
    }
}