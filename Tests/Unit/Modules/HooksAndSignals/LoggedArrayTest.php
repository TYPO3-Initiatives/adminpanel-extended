<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\HooksAndSignals;

use PHPUnit\Framework\TestCase;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\LoggedArray;

class LoggedArrayTest extends TestCase
{

    /**
     * @test
     */
    public function offsetExistsReturnsFalse(): void
    {
        $loggedArray = new LoggedArray([]);
        $result = $loggedArray->offsetExists('non-existing');
        self::assertFalse($result);
    }
}
