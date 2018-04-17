<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Modules\DoctrineDebug;

use Doctrine\DBAL\Logging\SQLLogger;

class CustomDebugStack implements SQLLogger
{
    /**
     * Executed SQL queries.
     *
     * @var array
     */
    public $queries = [];

    /**
     * If Debug Stack is enabled (log queries) or not.
     *
     * @var boolean
     */
    public $enabled = true;

    /**
     * @var float|null
     */
    public $start = null;

    /**
     * @var integer
     */
    public $currentQuery = 0;

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        if ($this->enabled) {
            $this->start = microtime(true);
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);
            // remove this method
            array_shift($backtrace);
            // remove doctrine execute query
            array_shift($backtrace);
            $this->queries[++$this->currentQuery] = [
                'sql' => $sql,
                'params' => $params,
                'types' => $types,
                'executionMS' => 0,
                'backtrace' => $backtrace
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if ($this->enabled) {
            $this->queries[$this->currentQuery]['executionMS'] = microtime(true) - $this->start;
        }
    }
}