<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\HooksAndSignals;

use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\LoggedArray;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LoggedArrayTest extends UnitTestCase
{
    protected $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
        $logManager = $this->prophesize(LogManager::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $logManager->getLogger(Argument::any())->willReturn($this->logger);
        GeneralUtility::setSingletonInstance(LogManager::class, $logManager->reveal());
    }

    /**
     * @test
     */
    public function offsetExistsReturnsFalseForNonExistingOffset(): void
    {
        $loggedArray = new LoggedArray([]);
        $result = $loggedArray->offsetExists('non-existing');
        self::assertFalse($result);
    }

    /**
     * @test
     */
    public function offsetExistsLogsAccessToNotSetOffsets(): void
    {
        $loggedArray = new LoggedArray([]);
        $loggedArray->offsetExists('some-hook-thing');
        $this->logger->log(LogLevel::DEBUG, '[\'some-hook-thing\']', Argument::containing('[\'some-hook-thing\']'))
            ->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function offsetExistsLogsAccessToExistingOffset(): void
    {
        $loggedArray = new LoggedArray(['some-hook-thing' => 'foo']);
        $loggedArray->offsetExists('some-hook-thing');
        $this->logger->log(LogLevel::DEBUG, '[\'some-hook-thing\']', Argument::containing('[\'some-hook-thing\']'))
            ->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function offsetExistsReturnsTrueForExistingOffset(): void
    {
        $loggedArray = new LoggedArray(['foo' => 'bar']);
        $result = $loggedArray->offsetExists('foo');
        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function offsetExistsPrefixesParentKey(): void
    {
        $loggedArray = new LoggedArray(['foo' => 'bar']);
        $loggedArray->setParent('[\'bar\']');
        $loggedArray->offsetExists('foo');
        $this->logger->log(LogLevel::DEBUG, '[\'bar\'][\'foo\']', Argument::containing('[\'bar\'][\'foo\']'))
            ->shouldHaveBeenCalled();
    }
}
