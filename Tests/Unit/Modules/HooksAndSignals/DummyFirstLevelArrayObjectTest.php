<?php
declare(strict_types=1);

namespace Psychomieze\AdminpanelExtended\Tests\Unit\Modules\HooksAndSignals;

use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\DummyFirstLevelArrayObject;
use Psychomieze\AdminpanelExtended\Modules\HooksAndSignals\LoggedArray;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DummyFirstLevelArrayObjectTest extends UnitTestCase
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
    public function offsetExistsReturnsParentOffsetExists(): void
    {
        $dummyFirstLevelArrayObject = new DummyFirstLevelArrayObject([]);
        $result = $dummyFirstLevelArrayObject->offsetExists('foo');
        self::assertFalse($result);
    }

    /**
     * @test
     */
    public function offsetExistsAlwaysReturnsTrueIfArrayHasSubLevels(): void
    {
        $dummyFirstLevelArrayObject = new DummyFirstLevelArrayObject(['foo' => ['bar' => 'baz']]);
        $result = $dummyFirstLevelArrayObject->offsetExists('foo');
        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function offsetExistsUsesLoggedArrayToLogNestedIssetCalls(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['foo'] = ['bar' => 'baz'];
        $dummyFirstLevelArrayObject = new DummyFirstLevelArrayObject([]);
        $dummyFirstLevelArrayObject->offsetExists('foo');
        self::assertInstanceOf(LoggedArray::class, $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['foo']);
    }

    /**
     * @test
     */
    public function offsetExistsSetsParentForLoggedArray(): void
    {
        $secondLevel = ['bar' => 'baz'];
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['foo'] = $secondLevel;
        $dummyFirstLevelArrayObject = new DummyFirstLevelArrayObject([]);
        $dummyFirstLevelArrayObject->offsetExists('foo');
        $loggedArray = new LoggedArray($secondLevel);
        $loggedArray->setParent('$GLOBALS[\'TYPO3_CONF_VARS\'][\'SC_OPTIONS\'][\'foo\']');

        self::assertEquals($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['foo'], $loggedArray);
    }
}
