<?php

namespace Tests\Command;

use App\Command\OptimizationJob;
use App\Entity\Campaign;
use App\Service\CampaignPublisherBlacklistUpdater;
use App\Tests\UnitTester;
use Codeception\Test\Unit;
use Psr\Log\Test\TestLogger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;

class ArticleRankCommandTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    /**
     * @throws \ReflectionException
     */
    public function testExecute()
    {
        /** @var OptimizationJob $command */
        $command = $this->tester->grabService(OptimizationJob::class);

        $expect = $this->getMockExpectationOfUpdater([1]);
        $mock = $expect->once()->getMock();
        $this->addMockToCommand($mock, $command, 'updater');

        $input = new ArgvInput([
            'command' => 'app:campaign:optimization'
        ]);
        $output = new NullOutput();

        $command->run($input, $output);

        $mock->mockery_verify();
    }

    public function testHandleException()
    {
        /** @var OptimizationJob $command */
        $command = $this->tester->grabService(OptimizationJob::class);

        $logger = new TestLogger();

        $expect = $this->getMockExpectationOfUpdater([1]);
        $mock = $expect->once()->andThrow(new \Exception('test'))->getMock();
        $this->addMockToCommand($mock, $command, 'updater');
        $this->addMockToCommand($logger, $command, 'logger');

        $input = new ArgvInput([
            'command' => 'app:campaign:optimization'
        ]);
        $output = new NullOutput();

        $command->run($input, $output);

        $this->tester->assertTrue($logger->hasErrorRecords());
        $mock->mockery_verify();
    }

    /**
     * @param $campaignId
     * @return \Mockery\Expectation
     */
    private function getMockExpectationOfUpdater(array $campaignIds)
    {
        $mock = \Mockery::mock(CampaignPublisherBlacklistUpdater::class)
            ->shouldReceive('updatePublishers')
            ->withArgs(function(Campaign $campaign) use ($campaignIds) {
                if (in_array($campaign->getId(), $campaignIds)) {
                    return true;
                }
                return false;
            })
        ;

        return $mock;
    }

    /**
     * @param $mock
     * @param $command
     * @throws \ReflectionException
     */
    private function addMockToCommand($mock, $command, $field)
    {
        $reflectionClass = new \ReflectionClass(get_class($command));
        $property = $reflectionClass->getProperty($field);
        $property->setAccessible(true);
        $property->setValue($command, $mock);
    }
}
