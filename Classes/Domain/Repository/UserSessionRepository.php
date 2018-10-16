<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Domain\Repository;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Session\SessionManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FrontendUserSessionRepository
 */
class UserSessionRepository
{

    private $sessionBackend;

    public const CONTEXT_FE = 'FE';
    public const CONTEXT_BE = 'BE';

    public function __construct($context, SessionManager $sessionManager = null)
    {
        $sessionManager = $sessionManager ?? GeneralUtility::makeInstance(SessionManager::class);
        $this->sessionBackend = $sessionManager->getSessionBackend($context);
    }

    /**
     * Find all active sessions for all frontend users.
     *
     * @return array
     */
    public function findAllActive(): array
    {
        $allSessions = $this->sessionBackend->getAll();

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
        usort(
            $allSessions,
            function ($session1, $session2) {
                return $session1['timestamp'] <=> $session2['timestamp'];
            }
        );
        return $allSessions;
    }

}
