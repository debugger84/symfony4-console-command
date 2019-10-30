<?php

namespace App\Tests\Unit\Example\Finder;

use App\Entity\Campaign;
use App\Entity\Enum\EventType;
use App\Entity\Event;
use App\Entity\Publisher;
use App\Entity\PublisherBlackListItem;
use App\EventListener\Campaign\SendEmailOnAddingToBlacklist;
use App\EventListener\Campaign\SendEmailOnRemovingFromBlacklist;
use App\Service\CampaignPublisherBlacklistUpdater;
use App\Service\EmailSender;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class CampaignPublisherBlacklistUpdaterTest extends Unit
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

    /**
     * @param $campaignId
     * @return \Mockery\Expectation
     */
    private function getMockExpectationOfSenderForDeletion($campaignId)
    {
        $mock = \Mockery::mock(EmailSender::class)
            ->shouldReceive('send')
            ->withArgs(function($to, $subject, $text) use ($campaignId) {
                if ($subject === 'You have been removed from the blacklist of the campaign #' . $campaignId) {
                    return true;
                }
                return false;
            })
        ;

        return $mock;
    }

    private function getMockExpectationOfSenderForAdding($campaignId)
    {
        $mock = \Mockery::mock(EmailSender::class)
            ->shouldReceive('send')
            ->withArgs(function($to, $subject, $text) use ($campaignId){
                if ($subject === 'You have been added to the blacklist of the campaign #' . $campaignId) {
                    return true;
                }
                return false;
            })
        ;

        return $mock;
    }

    /**
     * @param $mock
     * @param $listener
     * @throws \ReflectionException
     */
    private function addMockToListener($mock, $listener)
    {
        $reflectionClass = new \ReflectionClass(get_class($listener));
        $property = $reflectionClass->getProperty('sender');
        $property->setAccessible(true);
        $property->setValue($listener, $mock);
    }

    // tests
    public function testDontDelete()
    {
        /** @var SendEmailOnRemovingFromBlacklist $listener */
        $listener = $this->tester->grabService(SendEmailOnRemovingFromBlacklist::class);
        $expect = $this->getMockExpectationOfSenderForDeletion(1);
        $mock = $expect->never()->getMock();
        $this->addMockToListener($mock, $listener);

        $campaign = $this->tester->grabEntityFromRepository(Campaign::class, ['id' => 1]);

        /** @var CampaignPublisherBlacklistUpdater $updater */
        $updater = $this->tester->grabService(CampaignPublisherBlacklistUpdater::class);
        $updater->updatePublishers($campaign);

        $this->tester->assertCount(1, $campaign->getBlockedPublishers());
        $this->tester->assertEquals(1, $campaign->getBlockedPublishers()[0]->getId());
        $this->tester->seeInRepository(PublisherBlackListItem::class, ['id' => 1]);
        $mock->mockery_verify();
    }

    public function testDeleteDueToEventsAdded()
    {
        /** @var SendEmailOnRemovingFromBlacklist $listener */
        $listener = $this->tester->grabService(SendEmailOnRemovingFromBlacklist::class);
        $expect = $this->getMockExpectationOfSenderForDeletion(1);
        $mock = $expect->once()->getMock();
        $this->addMockToListener($mock, $listener);

        $campaign = $this->tester->grabEntityFromRepository(Campaign::class, ['id' => 1]);
        $publisher = $this->tester->grabEntityFromRepository(Publisher::class, ['id' => 1]);

        $this->tester->haveInRepository(Event::class, [
            'publisher' => $publisher,
            'campaign' => $campaign,
            'type' => EventType::purchase()->getValue()
        ]);

        /** @var CampaignPublisherBlacklistUpdater $updater */
        $updater = $this->tester->grabService(CampaignPublisherBlacklistUpdater::class);
        $updater->updatePublishers($campaign);

        $this->tester->assertCount(0, $campaign->getBlockedPublishers());
        $this->tester->dontSeeInRepository(PublisherBlackListItem::class, ['id' => 1]);
        $mock->mockery_verify();
    }

    public function testDeleteDueToThresholdChanged()
    {
        /** @var SendEmailOnRemovingFromBlacklist $listener */
        $listener = $this->tester->grabService(SendEmailOnRemovingFromBlacklist::class);
        $expect = $this->getMockExpectationOfSenderForDeletion(1);
        $mock = $expect->once()->getMock();
        $this->addMockToListener($mock, $listener);

        /** @var Campaign $campaign */
        $campaign = $this->tester->grabEntityFromRepository(Campaign::class, ['id' => 1]);
        $publisher = $this->tester->grabEntityFromRepository(Publisher::class, ['id' => 1]);

        $campaign->getOptimizationProps()->setThreshold(100);

        /** @var CampaignPublisherBlacklistUpdater $updater */
        $updater = $this->tester->grabService(CampaignPublisherBlacklistUpdater::class);
        $updater->updatePublishers($campaign);

        $this->tester->assertCount(0, $campaign->getBlockedPublishers());
        $this->tester->dontSeeInRepository(PublisherBlackListItem::class, ['id' => 1]);
        $mock->mockery_verify();
    }

    public function testDeleteDueToThresholdRateChanged()
    {
        /** @var SendEmailOnRemovingFromBlacklist $listener */
        $listener = $this->tester->grabService(SendEmailOnRemovingFromBlacklist::class);
        $expect = $this->getMockExpectationOfSenderForDeletion(1);
        $mock = $expect->once()->getMock();
        $this->addMockToListener($mock, $listener);

        /** @var Campaign $campaign */
        $campaign = $this->tester->grabEntityFromRepository(Campaign::class, ['id' => 1]);
        $publisher = $this->tester->grabEntityFromRepository(Publisher::class, ['id' => 1]);

        $campaign->getOptimizationProps()->setRatioThreshold(10);

        /** @var CampaignPublisherBlacklistUpdater $updater */
        $updater = $this->tester->grabService(CampaignPublisherBlacklistUpdater::class);
        $updater->updatePublishers($campaign);

        $this->tester->assertCount(0, $campaign->getBlockedPublishers());
        $this->tester->dontSeeInRepository(PublisherBlackListItem::class, ['id' => 1]);
        $mock->mockery_verify();
    }

    public function testAdd()
    {
        /** @var SendEmailOnAddingToBlacklist $listener */
        $listener = $this->tester->grabService(SendEmailOnAddingToBlacklist::class);
        $expect = $this->getMockExpectationOfSenderForAdding(1);
        $mock = $expect->once()->getMock();
        $this->addMockToListener($mock, $listener);

        /** @var Campaign $campaign */
        $campaign = $this->tester->grabEntityFromRepository(Campaign::class, ['id' => 1]);
        $publisher = $this->tester->grabEntityFromRepository(Publisher::class, ['id' => 2]);

        $this->tester->updateEntity(Event::class, [
            'type' => EventType::install()->getValue()
        ], 7);

        $campaign->getOptimizationProps()->setRatioThreshold(51);
        $campaign->getOptimizationProps()->setThreshold(0);

        /** @var CampaignPublisherBlacklistUpdater $updater */
        $updater = $this->tester->grabService(CampaignPublisherBlacklistUpdater::class);
        $updater->updatePublishers($campaign);

        $this->tester->assertCount(2, $campaign->getBlockedPublishers());
        $this->tester->seeInRepository(PublisherBlackListItem::class, ['id' => 1]);
        $this->tester->seeInRepository(PublisherBlackListItem::class, [
            'campaign' => $campaign,
            'publisher' => $publisher,
        ]);
    }
}
