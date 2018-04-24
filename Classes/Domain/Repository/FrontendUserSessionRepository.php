<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Domain\Repository;

use TYPO3\CMS\Core\Session\Backend\SessionBackendInterface;
use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

/**
 * Class FrontendUserSessionRepository
 */
class FrontendUserSessionRepository extends FrontendUserRepository
{
    /**
     * Find all active sessions for all backend users
     *
     * @return array
     */
    public function findAllActive(): array
    {
        $sessionBackend = $this->getSessionBackend();
        $allSessions = $sessionBackend->getAll();

        // Map array to correct keys
        $allSessions = array_map(
            function ($session) {
                return [
                    'id' => $session['ses_id'],
                    'ip' => $session['ses_iplock'],
                    'timestamp' => $session['ses_tstamp'],
                    'ses_userid' => $session['ses_userid']
                ];
            },
            $allSessions
        );

        // Sort by timestamp
        usort($allSessions, function ($session1, $session2) {
            return $session1['timestamp'] <=> $session2['timestamp'];
        });

        return $allSessions;
    }

    /**
     * @return \TYPO3\CMS\Core\Session\Backend\SessionBackendInterface
     */
    protected function getSessionBackend(): SessionBackendInterface
    {
        return GeneralUtility::makeInstance(SessionManager::class)->getSessionBackend('FE');
    }
}
