<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Domain\Repository;

use Psychomieze\AdminpanelExtended\Domain\Repository\UserSessionRepository;
use TYPO3\CMS\Core\Session\Backend\SessionBackendInterface;
use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class UserSessionRepositoryTest extends UnitTestCase
{

    const CONTEXT = 'FE';

    /**
     * @test
     */
    public function findAllActiveReturnsAllActiveSessions(): void
    {
        $sessionRow1 = [
            'ses_id' => '12345',
            'ses_iplock' => '127.*.*',
            'ses_tstamp' => 1539722983,
            'ses_userid' => 1
        ];
        $sessionRow2 = [
            'ses_id' => '12341',
            'ses_iplock' => '127.*.*',
            'ses_tstamp' => 1539722980,
            'ses_userid' => 1
        ];
        $sessionBackend = $this->prophesize(SessionBackendInterface::class);
        $sessionBackend->getAll()->willReturn([
            $sessionRow1,
            $sessionRow2
        ]);
        $sessionManager = $this->prophesize(SessionManager::class);
        $sessionManager->getSessionBackend(self::CONTEXT)->willReturn($sessionBackend->reveal());

        $userSessionRepository = new UserSessionRepository(self::CONTEXT, $sessionManager->reveal());
        $result = $userSessionRepository->findAllActive();

        self::assertSame([
            [
                'id' => '12341',
                'ip' => '127.*.*',
                'timestamp' => 1539722980,
                'ses_userid' => 1
            ],
            [
                'id' => '12345',
                'ip' => '127.*.*',
                'timestamp' => 1539722983,
                'ses_userid' => 1
            ]
        ], $result);
    }
}
