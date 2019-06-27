<?php

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\Fluid;

/*
 * This file is part of the TYPO3 Adminpanel Initiative.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Prophecy\Argument;
use Psychomieze\AdminpanelExtended\Modules\Fluid\TemplatePaths;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TemplatePathsTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;

    /**
     * @var \Psychomieze\AdminpanelExtended\Modules\Fluid\TemplatePaths
     */
    protected $subject;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy|\TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    protected function setUp(): void
    {
        $this->subject = new TemplatePaths();
        $this->logger = $this->prophesize(Logger::class);
    }

    /**
     * @test
     */
    public function getTemplateIdentifierShouldNotLogAnythingIfTemplateNameIsNotAString(): void
    {
        $this->logger->log(LogLevel::DEBUG, Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->mockLogger();
        $this->subject->getTemplateIdentifier();
    }

    /**
     * @test
     */
    public function getTemplateIdentifierLogsPathSetBySetTemplatePathAndFileName(): void
    {
        $this->mockLogger();
        $this->subject->setTemplatePathAndFilename('EXT:adminpanel_extended/Resources/Private/Templates/Fluid/Templates.html');
        $foo = $this->subject->getTemplateIdentifier();

    }

    protected function mockLogger(): void
    {
        $logManager = $this->prophesize(LogManager::class);
        $logManager->getLogger(Argument::any())->willReturn($this->logger->reveal());

        GeneralUtility::setSingletonInstance(LogManager::class, $logManager->reveal());
    }
}
